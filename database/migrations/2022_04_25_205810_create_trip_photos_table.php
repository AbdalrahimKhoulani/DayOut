<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_photos', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('trip_id')->references('id')->on('trips')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('trip_id');
            $table->longText('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_photos');
    }
};
