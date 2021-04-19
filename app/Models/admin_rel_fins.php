<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admin_rel_fins extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'admin_id','fin_product_id'
    ];
}
