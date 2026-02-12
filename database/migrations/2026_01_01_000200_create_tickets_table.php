<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // using 'name' instead of 'type' to allow more flexibility (e.g., "Standard", "VIP", etc.)
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('sold')->default(0); // Track how many tickets are sold to prevent overbooking.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};


