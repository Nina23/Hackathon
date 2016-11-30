<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_child', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('child');
            $table->dateTime('time');
            $table->string('note');
            $table->dateTime('end_time');
            $table->integer('event_type');
            $table->integer('notification_type')->default(1)->nullable();
            $table->integer('notification_time')->default(30)->nullable();
            $table->integer('event_shift')->default(0)->nullable();
            $table->integer('event_repeat')->default(1)->nullable();
            $table->boolean('event_all_day')->default(false)->nullable();
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
        Schema::drop('schedule_child');
    }
}
