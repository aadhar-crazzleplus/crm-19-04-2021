<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'title'
    ];

    public function product()
    {
        return $this->hasOne('App\Models\leads','product_id','id');
    }

}
