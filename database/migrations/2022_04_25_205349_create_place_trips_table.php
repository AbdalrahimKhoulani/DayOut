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
        Schema::create('place_trips', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('place_id')->references('id')->on('places')->cascadeOnUpdate()->cascadeOnDelete();
//            $table->foreignId('trip_id')->references('id')->on('trips')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('place_id');
            $table->unsignedBigInteger('trip_id');
            $table->integer('order');
            $table->text('description');
            $table->softDeletes();
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
        Schema::dropIfExists('place_trips');
    }
};
