<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleNet extends Model
{
    protected $table = 'schedule_net';
    protected $fillable=['child','day','time','interval'];
}
