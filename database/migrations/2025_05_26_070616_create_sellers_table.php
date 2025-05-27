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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1 = company, 2 = individual'); // 1 => Company, 2 => Individual
            // Common fields
            $table->string('full_name');
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('country');
            $table->string('password');
            $table->text('source_of_goods')->nullable(); // For individuals
            // Company specific fields
            $table->string('company_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('director_name')->nullable();
            $table->string('director_email')->nullable();
            $table->string('director_phone')->nullable();
            // File upload fields (can be stored as file paths or JSON)
            $table->string('certificate_of_incorporation')->nullable();
            $table->string('valid_trade_license')->nullable();
            $table->string('passport_copy_authorised')->nullable();
            $table->string('ubo_declaration')->nullable();
            $table->string('passport_copy')->nullable(); // For individuals
            $table->string('proof_of_ownership')->nullable(); // For individuals
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
        Schema::dropIfExists('sellers');
    }
};
