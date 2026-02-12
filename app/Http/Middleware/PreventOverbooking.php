<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventOverbooking
{
    /**
     * Prevent booking more tickets than are available for a given ticket.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ticket = $request->route('ticket');

        if (! $ticket instanceof Ticket) {
            $ticketId = $request->route('ticket');
            $ticket = Ticket::find($ticketId);
        }

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
                'errors' => [],
            ], 404);
        }

        $quantity = (int) $request->input('quantity', 1);
        $available = $ticket->quantity - $ticket->sold;

        if ($quantity > $available) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough tickets available.',
                'errors' => ['Only '.$available.' tickets left for this type.'],
            ], 422);
        }

        return $next($request);
    }
}


