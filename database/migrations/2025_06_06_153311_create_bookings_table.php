<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_id')->unique();
            $table->unsignedBigInteger('bidder_id');
            $table->string('room_name')->nullable();
            $table->string('room_type');
            $table->time('start_time');
            $table->date('date_for_reservation');

            // $table->unsignedBigInteger('booking_lot_id')->nullable();
            $table->json('booking_lot_id')->nullable();
            $table->boolean('lot_booking_flag')->default(false)->comment('0 = no, 1 = yes');

            // $table->unsignedBigInteger('requested_lot_id')->nullable();
            $table->json('requested_lot_id')->nullable();
            $table->boolean('lot_requested_flag')->default(false)->comment('0 = no, 1 = yes');

            $table->integer('timer')->nullable()->comment('in seconds or minutes based on use-case');
            $table->string('timer_status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
