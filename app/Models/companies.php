<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class companies extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_cat_id', 'org_id', 'bank_id', 'company_code', 'company_name', 'city_id', 'state_id', 'address', 'pincode_id', 'phone_number', 'status'
    ];

    public function lead_profile()
    {
        return $this->hasOne('App\Models\lead_profiles','company_id','id');
    }
}
