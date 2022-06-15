<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar')->default('default.png');
        });
        Schema::create('usersfavorites', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('barber_id');
        });
        Schema::create('userappointments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('barber_id');
            $table->dateTime('ap_datetime');
        });

        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->default('default.png');
            $table->float('star')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
        Schema::create('barberphotos', function (Blueprint $table) {
            $table->id();
            $table->integer('barber_id');
            $table->string('url');
        });
        Schema::create('barberreviews', function (Blueprint $table) {
            $table->id();
            $table->integer('barber_id');
            $table->float('rate');
        });
        Schema::create('barberservices', function (Blueprint $table) {
            $table->id();
            $table->integer('barber_id');
            $table->string('name');
            $table->float('price');
        });
        Schema::create('barbertestimonials', function (Blueprint $table) {
            $table->id();
            $table->integer('barber_id');
            $table->string('name');
            $table->float('rate');
            $table->string('body');
        });

        Schema::create('barberavailability', function (Blueprint $table) {
            $table->id();
            $table->integer('barber_id');
            $table->integer('weekday');
            $table->text('hours');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('usersfavorites');
        Schema::dropIfExists('userappointments');
        Schema::dropIfExists('barbers');
        Schema::dropIfExists('barberphotos');
        Schema::dropIfExists('barberreviews');
        Schema::dropIfExists('barberservices');
        Schema::dropIfExists('barbertestimonials');
        Schema::dropIfExists('barberavailability');
    }
}
