<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $bookings = Booking::with(['ticket.event', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return $this->successResponse('Bookings fetched successfully', $bookings);
    }

    public function store(Request $request, int $ticket)
    {
        $user = $request->user();

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Resolve the ticket manually so we can return a clean JSON 404 if it's missing.
        $ticketModel = Ticket::find($ticket);

        if (! $ticketModel) {
            return $this->errorResponse('Ticket not found.', [], 404);
        }

        $booking = DB::transaction(function () use ($ticketModel, $user, $data) {
            // Lock row to avoid race conditions when booking.
            $ticketModel->refresh();

            $available = $ticketModel->quantity - $ticketModel->sold;
            if ($data['quantity'] > $available) {
                abort(422, 'Not enough tickets available.');
            }

            $booking = Booking::create([
                'user_id' => $user->id,
                'ticket_id' => $ticketModel->id,
                'quantity' => $data['quantity'],
                'status' => Booking::STATUS_PENDING,
            ]);

            $ticketModel->increment('sold', $data['quantity']);

            // Queue notification to be sent to the customer.
            $user->notify(new BookingConfirmedNotification($booking));

            return $booking->load('ticket.event');
        });

        return $this->successResponse('Booking created successfully', $booking, 201);
    }

    public function cancel(Request $request, Booking $booking)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($booking->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only cancel your own bookings.'], 403);
        }

        if ($booking->status === Booking::STATUS_CANCELLED) {
            return $this->errorResponse('Booking already cancelled', [], 422);
        }

        DB::transaction(function () use ($booking) {
            $booking->status = Booking::STATUS_CANCELLED;
            $booking->save();

            // Return tickets to pool.
            $booking->ticket->decrement('sold', $booking->quantity);
        });

        return $this->successResponse('Booking cancelled successfully', $booking->fresh());
    }
}


