<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users with deterministic emails for easy testing.
        $admins = collect();
        foreach (range(1, 2) as $i) {
            $admins->push(
                User::factory()
                    ->admin()
                    ->create([
                        'name' => "Admin {$i}",
                        'email' => "admin{$i}@example.com",
                    ])
            );
        }

        // Create organizer users with deterministic emails.
        $organizers = collect();
        foreach (range(1, 3) as $i) {
            $organizers->push(
                User::factory()
                    ->organizer()
                    ->create([
                        'name' => "Organizer {$i}",
                        'email' => "organizer{$i}@example.com",
                    ])
            );
        }

        // Create customer users with deterministic emails.
        $customers = collect();
        foreach (range(1, 10) as $i) {
            $customers->push(
                User::factory()
                    ->customer()
                    ->create([
                        'name' => "Customer {$i}",
                        'email' => "customer{$i}@example.com",
                    ])
            );
        }

        // Create events for organizers (total 5 events).
        $events = collect();
        for ($i = 0; $i < 5; $i++) {
            $organizer = $organizers->random();
            $events->push(
                Event::factory()->create([
                    'user_id' => $organizer->id,
                ])
            );
        }

        // Create 15 tickets across the 5 events.
        $tickets = collect();
        foreach ($events as $event) {
            $tickets = $tickets->merge(
                Ticket::factory()
                    ->count(3)
                    ->create([
                        'event_id' => $event->id,
                    ])
            );
        }

        // Create 20 bookings for customers on random tickets.
        $bookings = collect();
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $ticket = $tickets->random();

            $quantity = fake()->numberBetween(1, 3);

            $booking = Booking::create([
                'user_id' => $customer->id,
                'ticket_id' => $ticket->id,
                'quantity' => $quantity,
                'status' => Booking::STATUS_CONFIRMED,
            ]);

            // Increment sold counter to keep track of capacity usage.
            $ticket->increment('sold', $quantity);

            $bookings->push($booking);
        }

        // Optionally create payments for a subset of bookings.
        foreach ($bookings as $booking) {
            // Roughly 70% of bookings have a payment.
            if (fake()->boolean(70)) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->ticket->price * $booking->quantity,
                    'status' => fake()->boolean(80) ? 'successful' : 'failed',
                    'transaction_reference' => 'TX-'.$booking->id.'-'.fake()->numerify('########'),
                ]);
            }
        }
    }
}
