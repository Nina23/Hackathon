<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id')->unique();
            $table->string('email');
            $table->string('password');
            $table->string('number')->unique();
            $table->string('address')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('image')->nullable();
            $table->tinyInteger('status');
            $table->string('class')->nullable();
            $table->tinyInteger('sex')->nullable();
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
        Schema::drop('child');
    }
}
