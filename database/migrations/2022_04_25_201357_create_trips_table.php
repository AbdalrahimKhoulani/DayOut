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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('title');
           // $table->foreignId('organizer_id')->references('id')->on('organizers')->cascadeOnUpdate()->cascadeOnDelete();
           // $table->foreignId('trip_status_id')->references('id')->on('trip_statuses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('trip_status_id');

            $table->text('description');
            $table->dateTime('begin_date');
            $table->dateTime('expire_date');
            $table->float('price');
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
        Schema::dropIfExists('trips');
    }
};
