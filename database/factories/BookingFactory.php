<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->customer(),
            'ticket_id' => Ticket::factory(),
            'quantity' => fake()->numberBetween(1, 4),
            'status' => Booking::STATUS_CONFIRMED,
        ];
    }
}


