<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead_is_loans extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_profile_id', 'total_rem_loan', 'monthly_emi'
    ];
}
