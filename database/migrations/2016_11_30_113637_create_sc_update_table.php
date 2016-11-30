<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScUpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_child', function (Blueprint $table) {
            $table->dateTime('time')->change();
            $table->dateTime('end_time')->change();
        });
    }


}
