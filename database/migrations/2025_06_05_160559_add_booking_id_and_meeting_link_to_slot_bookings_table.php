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
        Schema::table('slot_bookings', function (Blueprint $table) {
            $table->string('booking_id')->after('id');
            $table->string('meeting_link')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slot_bookings', function (Blueprint $table) {
            $table->dropColumn('booking_id');
            $table->dropColumn('meeting_link');
        });
    }
};
