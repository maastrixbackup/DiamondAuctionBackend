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
            $table->tinyInteger('type')->comment('1 = company, 2 = individual');
            $table->string('full_name');
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('country');
            $table->string('password');
            $table->text('source_of_goods')->nullable();

            // Company specific fields
            $table->string('company_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('director_name')->nullable();
            $table->string('director_email')->nullable();
            $table->string('director_phone')->nullable();

            // Documents and their statuses
            $table->string('certificate_of_incorporation')->nullable();
            $table->unsignedTinyInteger('certificate_of_incorporation_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');

            $table->string('valid_trade_license')->nullable();
            $table->unsignedTinyInteger('valid_trade_license_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');

            $table->string('passport_copy_authorised')->nullable();
            $table->unsignedTinyInteger('passport_copy_authorised_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');

            $table->string('ubo_declaration')->nullable();
            $table->unsignedTinyInteger('ubo_declaration_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');

            $table->string('passport_copy')->nullable(); // For individuals
            $table->unsignedTinyInteger('passport_copy_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');

            $table->string('proof_of_ownership')->nullable(); // For individuals
            $table->unsignedTinyInteger('proof_of_ownership_status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');

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
