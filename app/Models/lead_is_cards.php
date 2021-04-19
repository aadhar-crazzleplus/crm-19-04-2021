<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead_is_cards extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_profile_id', 'bank_id', 'total_limit', 'ava_limit', 'card_vintage'
    ];
}
