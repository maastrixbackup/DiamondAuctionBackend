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
        Schema::table('bidders', function (Blueprint $table) {
            $table->string('referal_code')->nullable()->after('vip_bidding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidders', function (Blueprint $table) {
            $table->dropColumn('referal_code');
        });
    }
};
