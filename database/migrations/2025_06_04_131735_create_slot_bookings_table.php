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
        Schema::create('slot_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id');
            $table->unsignedBigInteger('slot_id')->nullable();
            $table->string('bidder_name');
            $table->string('room_name')->nullable();
            $table->string('room_type');
            $table->time('start_time');
            $table->date('date_for_reservation');
            $table->unsignedBigInteger('bidder_id');
            $table->tinyInteger('status')->default(0)->comment('0->pending, 1->approved, 2->rejected, 3-> Requested, 4->Cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_bookings');
    }
};
