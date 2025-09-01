<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'slug',
        'description',
        'rate',
    ];
}
