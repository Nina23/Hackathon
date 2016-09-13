<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location';
    protected $fillable=['child','lang','lat','time_of_location','speed','state','accuracy','provider'];
}
