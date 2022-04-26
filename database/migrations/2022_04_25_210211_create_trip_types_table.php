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
        Schema::create('trip_types', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('trip_id')->references('id')->on('trips')->cascadeOnUpdate()->cascadeOnDelete();
//            $table->foreignId('type_id')->references('id')->on('types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('type_id');
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
        Schema::dropIfExists('trip_types');
    }
};
