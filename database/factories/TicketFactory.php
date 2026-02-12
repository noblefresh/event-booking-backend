<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(['Standard', 'VIP', 'Early Bird', 'Backstage']),
            'price' => fake()->randomFloat(2, 10, 300),
            'quantity' => fake()->numberBetween(50, 200),
            'sold' => 0,
        ];
    }
}


