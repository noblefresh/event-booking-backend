<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
    }

    public function store(Request $request, Booking $booking)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($booking->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only pay for your own bookings.'], 403);
        }

        if ($booking->payment) {
            return $this->errorResponse('Payment already exists for this booking.', [], 422);
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $amount = (float) $data['amount'];

        $result = $this->paymentService->process($booking, $amount);

        $payment = DB::transaction(function () use ($booking, $result) {
            return Payment::create([
                'booking_id' => $booking->id,
                'amount' => $result['amount'],
                'status' => $result['status'],
                'transaction_reference' => $result['success'] ? 'TX-'.uniqid() : null,
            ]);
        });

        return $this->successResponse('Payment processed', [
            'payment' => $payment,
            'result' => $result,
        ]);
    }

    public function show(Request $request, Payment $payment)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($payment->booking->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only view your own payments.'], 403);
        }

        return $this->successResponse('Payment details', $payment->load('booking.ticket.event'));
    }
}


