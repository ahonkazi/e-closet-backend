<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[
        'vendor_id',
        'title',
        'code',
        'discription',
        'slug',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'is_approved',
        'is_published',
        'is_featured',
        'product_image'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
public function sub_category(){
        return $this->belongsTo(SubCategory::class);
    }

    public function sub_sub_category(){
        return $this->belongsTo(SubSubCategory::class);
    }
    public function productStock(){
        return $this->hasMany(ProductStock::class);
    }
}
