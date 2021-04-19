<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rel_fin extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id','fin_product_id'
    ];

    // public function user()
    // {
    //     return $this->belongsTo('App\Models\User');
    // }
}
