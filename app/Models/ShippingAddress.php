<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'country',
        'district',
        'sub_district',
        'street_address',
        'appartment_number',
        'postal_code',
        'phone',

    ];
}
