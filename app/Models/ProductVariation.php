<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProductVariation extends Model
{
    use HasFactory;
    protected $fillable =['product_id','vendor_id','variation_id','is_primary'];
 public function options(){
    return $this->hasMany(ProductVariationOption::class);
 }
  public function variation(){
     return $this->belongsTo(Variation::class);
 }
}
