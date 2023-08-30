<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'vendor_id',
        'product_code',
        'title',
        'discription',
        'price',
        'discount',
        'category_id',
        'subcategory_id',
        'is_approved',
        'product_image',
        'slug'
    ];

}
