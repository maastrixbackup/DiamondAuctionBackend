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
            $table->decimal('carat', 8, 2)->nullable()->after('fluorescence');
            $table->string('search_by_type')->nullable()->after('carat');
            $table->string('availability')->nullable()->after('search_by_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn(['carat', 'search_by_type', 'availability']);
        });
    }
};
