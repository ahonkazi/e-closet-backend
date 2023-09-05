<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable = ['category_id','creator_id','name','slug','is_approved'];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function subSubCategory(){
        return $this->hasMany(SubSubCategory::class);
    }
}
