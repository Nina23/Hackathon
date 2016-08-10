<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditPhone extends Model
{
    protected $table = 'credit_phone';
    protected $fillable=['parents','child','time_of_creation','amount'];
}
