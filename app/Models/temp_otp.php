<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temp_otp extends Model
{
    use HasFactory;
    protected $fillable = [
        'mobile_no', 'otp'
    ];
}
