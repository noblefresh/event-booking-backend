<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the application.
| These routes are automatically assigned the "api" middleware group
| and will be prefixed with /api.
|
*/

// Auth endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store'])->middleware('role:admin,organizer');
    Route::put('/events/{event}', [EventController::class, 'update'])->middleware('role:admin,organizer');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->middleware('role:admin,organizer');

    // Tickets
    Route::post('/events/{event}/tickets', [TicketController::class, 'store'])->middleware('role:admin,organizer');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->middleware('role:admin,organizer');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->middleware('role:admin,organizer');

    // Bookings
    Route::post('/tickets/{ticket}/bookings', [BookingController::class, 'store'])->middleware(['role:customer', 'prevent_overbooking']);
    Route::get('/bookings', [BookingController::class, 'index'])->middleware('role:customer');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->middleware('role:customer');

    // Payments
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])->middleware('role:customer');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->middleware('role:customer');
});


