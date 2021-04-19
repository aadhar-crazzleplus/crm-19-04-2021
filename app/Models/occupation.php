<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class occupation extends Model
{
    use HasFactory;

    protected $fillable = [
        'occu_type_id', 'occu_title'
    ];

    public function lead_profile()
    {
        return $this->hasOne('App\Models\lead_profiles','occupation_id','id');
    }
}
