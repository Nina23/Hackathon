<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentsChild extends Model
{
    protected $table = 'parents_child';
    protected $fillable=['parents','child'];
}
