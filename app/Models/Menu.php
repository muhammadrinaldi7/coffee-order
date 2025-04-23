<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'categories_id','name', 'description', 'price', 'image_url', 'is_available'
    ];
    protected $hidden = [
        'categories_id','categories',
    ];
    protected $appends = ['categories_name'];
    
    public function categories()
    {
        return $this->belongsTo(Categories::class);
    }
    
        public function getCategoriesNameAttribute()
    {
        return $this->categories?->name;
    }
}
