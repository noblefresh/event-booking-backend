<?php

namespace App\Services;

use App\Models\Booking;

class PaymentService
{
    /**
     * Simulate processing a payment for a booking.
     *
     * Uses a random boolean to indicate success / failure and returns
     * the final status and amount charged.
     */
    public function process(Booking $booking, float $amount): array
    {
        $success = (bool) random_int(0, 1);

        return [
            'success' => $success,
            'status' => $success ? 'successful' : 'failed',
            'amount' => $amount,
        ];
    }
}


