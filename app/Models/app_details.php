<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class app_details extends Model
{
    use HasFactory;
    protected $fillable = [
        'old_version', 'new_version'
    ];
}
