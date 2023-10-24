<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id','country','district','sub_district','street_address','appartment_number','postal_code','phone','address_type_no'];

}
