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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')
                ->constrained('sellers')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');
            $table->string('type');
            $table->string('color')->nullable();
            $table->string('weight')->nullable();
            $table->string('size')->nullable();
            $table->string('stone')->nullable();
            $table->string('shape')->nullable();
            $table->text('notes')->nullable();
            $table->string('batch_code')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment('0 = pending, 1 = active, 2 = sold');
            $table->string('report_number')->nullable();
            $table->string('colour_grade')->nullable();
            $table->string('colour_origin')->nullable();
            $table->string('colour_distribution')->nullable();
            $table->string('polish')->nullable();
            $table->string('symmetry')->nullable();
            $table->string('fluorescence')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
