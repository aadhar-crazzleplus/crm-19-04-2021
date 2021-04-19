<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pr_banners extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'image', 'url', 'status'
    ];
}
