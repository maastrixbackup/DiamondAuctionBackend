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
        Schema::create('bidders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1 = company, 2 = individual');

            // Common
            $table->string('full_name');
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('country');
            $table->string('password');

            // Company fields
            $table->string('company_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('director_name')->nullable();
            $table->string('director_email')->nullable();
            $table->string('director_phone')->nullable();

            // Company documents
            $table->string('certificate_of_incorporation')->nullable();
            $table->string('valid_trade_license')->nullable();
            $table->string('passport_copy_authorised')->nullable();
            $table->string('ubo_declaration')->nullable();

            // Individual documents
            $table->string('passport_copy')->nullable();
            $table->string('proof_of_address')->nullable();

            $table->unsignedTinyInteger('kyc_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');
            $table->unsignedTinyInteger('account_status')->default(0)->comment('0 = pending, 1 = active, 2 = suspended');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidders');
    }
};
