<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    protected $table = 'applications';
    protected $fillable=['child','name_of_package','name_of_application','status'];
}
