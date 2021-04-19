<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    use HasFactory;
    protected $fillable = [
        'state_id', 'state_name', 'status'
    ];

    public function pincode()
    {
        return $this->hasMany('App\Models\pincode');
    }
}
