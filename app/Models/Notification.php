<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'notification_type_id',
        'read_status',
        'from_id',
        'receiver_id',
        'receiver_role_id',
        'ref_id',
        'tamplate'
    ];
}
