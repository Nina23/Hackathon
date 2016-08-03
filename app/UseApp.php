<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UseApp extends Model
{
    protected $table = 'use_app';
    protected $fillable=['child','application','interval','time_of_creation'];
}
