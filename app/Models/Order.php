<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'unique_id',
        'customer_id',
        'shipping_id',
        'payment_method',
        'total_price',
        'order_status'
    ];
}
