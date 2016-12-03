<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Reports extends Model
{
    
    protected $table = 'reports';
    protected $fillable = [
        'name', 'category', 'description','latitude','longitude','failed'
    ];
    
   
    
}
