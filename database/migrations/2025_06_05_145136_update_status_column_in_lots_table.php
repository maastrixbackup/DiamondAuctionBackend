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
        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('0 = pending, 1 = live, 2 = sold')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')
                ->default(0)
                ->comment('0 = pending, 1 = active, 2 = sold')
                ->change();
        });
    }
};
