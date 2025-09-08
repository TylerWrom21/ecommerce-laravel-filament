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
        'category_id',
        'quantity',
        'tags',
        'description',
        'thumbnail',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
}
