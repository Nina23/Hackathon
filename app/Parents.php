<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    protected $table = 'parents';
    protected $fillable=['unique_id','email','password','number','address','first_name','last_name','image','status','activated','frendino_pro'];
}
