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
        Schema::create('bulk_biddings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bidder_id')->nullable();
            $table->integer('lot_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamp('bidding_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_biddings');
    }
};
