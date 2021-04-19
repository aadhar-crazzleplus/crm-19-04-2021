<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class social_card_rels extends Model
{
    use HasFactory;
    protected $fillable = [
        'social_card_id', 'social_cat_id'
    ];
}
