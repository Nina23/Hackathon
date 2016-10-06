<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolSettings extends Model
{
    protected $table = 'school_settings';
    protected $fillable=['child','school_state','week_switch'];
}
