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
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('kyc_document')->nullable()->after('proof_of_ownership_status');
            $table->unsignedTinyInteger('kyc_document_status')->default(0)->after('kyc_document')->comment('0 = pending, 1 = approved, 2 = rejected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['kyc_document', 'kyc_document_status']);
        });
    }
};
