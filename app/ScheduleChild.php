<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleChild extends Model
{
    protected $table = 'schedule_child';
    protected $fillable=['child','time','note'];
}
