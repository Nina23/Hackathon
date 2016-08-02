<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('number')->nullable();
            $table->string('address')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('image')->nullable();
            $table->tinyInteger('status')->nullable();
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
        Schema::drop('parents');
    }
}
