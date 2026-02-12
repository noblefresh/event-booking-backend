<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'successful', 'failed']);

        return [
            'booking_id' => Booking::factory(),
            'amount' => fake()->randomFloat(2, 20, 500),
            'status' => $status,
            'transaction_reference' => $status === 'successful'
                ? 'TX-'.fake()->unique()->numerify('########')
                : null,
        ];
    }
}


