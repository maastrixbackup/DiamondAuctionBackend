<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->string('clarity')->nullable()->after('shape'); // adjust the position if needed
        });
    }

    public function down()
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn('clarity');
        });
    }
};
