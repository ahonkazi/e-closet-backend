<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationOption extends Model
{
    use HasFactory;
    protected $fillable =['product_id','vendor_id','variation_id','value'];
}
