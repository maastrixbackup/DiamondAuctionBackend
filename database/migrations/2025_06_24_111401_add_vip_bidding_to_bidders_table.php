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
            $table->unsignedTinyInteger('vip_bidding')->default(0)->comment('0 = no, 1 = yes')->after('account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidders', function (Blueprint $table) {
            $table->dropColumn('vip_bidding');
        });
    }
};
