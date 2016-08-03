<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $table = 'child';
    protected $fillable=['unique_id','email','password','number','address','first_name','last_name','image','status','class','sex'];
}
