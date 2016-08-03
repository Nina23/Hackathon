<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleApp extends Model
{
    protected $table = 'schedule_app';
    protected $fillable=['application','day','time','interval'];
}
