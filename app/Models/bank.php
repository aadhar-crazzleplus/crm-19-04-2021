<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_title', 'image_url', 'status'
    ];

    public function relbank()
    {
        return $this->hasOne('App\Models\rel_bank');
    }
}
