<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $user = $request->user();

        if ($user->role === 'organizer' && $event->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only add tickets to your own events.'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $data['event_id'] = $event->id;

        $ticket = Ticket::create($data);

        return $this->successResponse('Ticket created successfully', $ticket, 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if ($user->role === 'organizer' && $ticket->event->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only modify tickets for your own events.'], 403);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);

        $ticket->update($data);

        return $this->successResponse('Ticket updated successfully', $ticket);
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if ($user->role === 'organizer' && $ticket->event->user_id !== $user->id) {
            return $this->errorResponse('Forbidden', ['You can only delete tickets for your own events.'], 403);
        }

        $ticket->delete();

        return $this->successResponse('Ticket deleted successfully');
    }
}


