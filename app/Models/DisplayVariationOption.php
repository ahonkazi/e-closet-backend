<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplayVariationOption extends Model
{
    use HasFactory;
    protected $fillable = ['value','display_variation_id','color_code'];
    
}
