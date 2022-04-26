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
        Schema::create('customer_trips', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('customer_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
//            $table->foreignId('trip_id')->references('id')->on('trips')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('trip_id');
            $table->boolean('checkout');
            $table->integer('rate')->nullable();
            $table->text('rate_comment')->nullable();
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
        Schema::dropIfExists('customer_trips');
    }
};
