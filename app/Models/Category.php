<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['creator_id','name','slug','is_approved','icon_id'];

    public function sub_categories(){
        return $this->hasMany(SubCategory::class);
    }
    public function icon(){
        return $this->belongsTo(CategoryIcon::class);
    }


}
