<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'slug',
        'price',
        'status',
        'quantity',
        'tags',
        'description',
        'thumbnail',
    ];
}
