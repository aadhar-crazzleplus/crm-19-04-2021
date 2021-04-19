<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_log extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'admin_id', 'ip', 'mobile_no', 'activity_at', 'lags_longs', 'device_id', 'app_version', 'mobile_type'
    ];
}
