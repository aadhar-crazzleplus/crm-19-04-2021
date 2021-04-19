<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrBanner extends Model
{
    use HasFactory;

    protected $table = 'pr_banners';

    public function getImageAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
