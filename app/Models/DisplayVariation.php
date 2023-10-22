<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplayVariation extends Model
{
    use HasFactory;
    protected $fillable = ['name','is_primary'];
    
}
