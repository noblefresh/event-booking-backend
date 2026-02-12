<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your booking is confirmed')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your booking has been confirmed.')
            ->line('Event: '.$this->booking->ticket->event->title)
            ->line('Ticket: '.$this->booking->ticket->name)
            ->line('Quantity: '.$this->booking->quantity)
            ->line('Thank you for using our event booking platform!');
    }
}


