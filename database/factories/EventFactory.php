<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 days', '+1 month');
        $end = (clone $start)->modify('+'.fake()->numberBetween(2, 6).' hours');

        return [
            'user_id' => User::factory()->organizer(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'start_date' => $start,
            'end_date' => $end,
        ];
    }
}


