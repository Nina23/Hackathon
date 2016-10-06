<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSchoolSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child');
            $table->tinyInteger('school_state');
            $table->tinyInteger('week_switch');
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
        Schema::drop('school_settings');
    }
}
