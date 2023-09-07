<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProductStock extends Model
{
    use HasFactory;
    protected $fillable =['product_id',
    'vendor_id',
    'primary_option_id',
    'secondary_option_id',
    'image',
    'price',
    'discount_in_percent',
    'stock',
];
    
    public function primary_option(){
        return $this->belongsTo(ProductVariationOption::class);
    }
        public function secondary_option(){
        return $this->belongsTo(ProductVariationOption::class);
    }
}
