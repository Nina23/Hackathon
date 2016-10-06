<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleChild extends Model
{
    protected $table = 'schedule_child';
    protected $fillable=['child','time','note','end_time','event_type','notification_type','notification_time','event_shift','event_repeat','event_all_day'];
}
