<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child');
            $table->string('lang');
            $table->string('lat');
            $table->string('state')->nullable();
            $table->string('speed')->nullable();
            $table->string('accuracy')->nullable();
            $table->string('provider')->nullable();
            $table->timestamp('time_of_location');
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
        Schema::drop('location');
    }
}
