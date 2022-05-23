<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_trips', function (Blueprint $table) {

            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete()->cascadeOnUpdate();

        });

        Schema::table('favorite_places', function (Blueprint $table) {

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete()->cascadeOnUpdate();

        });

        Schema::table('place_photos', function (Blueprint $table) {

            $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete()->cascadeOnUpdate();

        });

        Schema::table('place_trips', function (Blueprint $table) {

            $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('trips', function (Blueprint $table) {

            $table->foreign('trip_status_id')->references('id')->on('trip_statuses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('organizer_id')->references('id')->on('organizers')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('trip_types', function (Blueprint $table) {
            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('type_id')->references('id')->on('types')->cascadeOnDelete()->cascadeOnUpdate();
        });


        Schema::table('passengers', function (Blueprint $table) {
            $table->foreign('customer_trip_id')->references('id')->on('customer_trips');
        });


        Schema::table('user_reports', function (Blueprint $table) {

            $table->foreign('reporter_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('target_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });


        Schema::table('user_roles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete()->cascadeOnUpdate();
        });


        Schema::table('promotion_requests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('status_id')->references('id')->on('promotion_statuses')->cascadeOnDelete()->cascadeOnUpdate();
        });


        Schema::table('organizers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });


        Schema::table('followers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('organizer_id')->references('id')->on('organizers')->cascadeOnDelete()->cascadeOnUpdate();
        });


        Schema::table('place_suggestions', function (Blueprint $table) {
            $table->foreign('organizer_id')->references('id')->on('organizers')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('trip_photos',function (Blueprint $table){
           $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->foreign('organizer_id')->references('id')->on('organizers')->cascadeOnDelete()->cascadeOnUpdate();;
        });


        Schema::table('poll_choices', function (Blueprint $table) {
            $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete()->cascadeOnUpdate();;
        });

        Schema::table('customer_poll_choices', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('poll_choice_id')->references('id')->on('poll_choices')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('trip_photos', function (Blueprint $table) {
            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete()->cascadeOnUpdate();;
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();;
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_trips', function (Blueprint $table) {

            $table->dropForeign('customer_id');
            $table->dropForeign('trip_id');

        });

        Schema::table('favorite_places', function (Blueprint $table) {

            $table->dropForeign('user_id');
            $table->dropForeign('place_id');

        });
        Schema::table('trip_photos',function (Blueprint $table){
            $table->dropForeign('trip_id');
        });
        Schema::table('place_photos', function (Blueprint $table) {

            $table->dropForeign('place_id');

        });

        Schema::table('place_trips', function (Blueprint $table) {

            $table->dropForeign('place_id');
            $table->dropForeign('trip_id');
        });

        Schema::table('trips', function (Blueprint $table) {

            $table->dropForeign('trip_status_id');
            $table->dropForeign('organizer_id');
        });

        Schema::table('trip_types', function (Blueprint $table) {

            $table->dropForeign('trip_id');
            $table->dropForeign('type_id');
        });


        Schema::table('passengers', function (Blueprint $table) {

            $table->dropForeign('customer_trip_id');

        });

        Schema::table('user_reports', function (Blueprint $table) {
            $table->dropForeign('reporter_id');
        });


        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropForeign('user_id')->references('id');
            $table->dropForeign('role_id')->references('id');
        });


        Schema::table('promotion_requests', function (Blueprint $table) {
            $table->dropForeign('user_id');
            $table->dropForeign('status_id');
        });


        Schema::table('organizers', function (Blueprint $table) {
            $table->dropForeign('user_id');
        });


        Schema::table('followers', function (Blueprint $table) {
            $table->dropForeign('user_id');
            $table->dropForeign('organizer_id');
        });

        Schema::table('place_suggestions', function (Blueprint $table) {
            $table->dropForeign('organizer_id');
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->dropForeign('organizer_id');
        });


        Schema::create('poll_choices', function (Blueprint $table) {
            $table->dropForeign('poll_id');
        });


        Schema::table('trip_photos', function (Blueprint $table) {
            $table->dropForeign('trip_id');

        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('user_id');

        });
    }
};
