<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUseAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('use_app', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child');
            $table->integer('application');
            $table->string('interval');
            $table->timestamp('time_of_creation');
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
        Schema::drop('use_app');
    }
}
