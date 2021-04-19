<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pincode extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_name', 'region_name', 'division_name', 'office_name', 'pincode', 'office_type', 'delivery', 'created_by', 'modified_by', 'district', 'district_name', 'city_id'
    ];

    public function address()
    {
        return $this->hasOne('App\Models\Address');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\city');
        // return $this->belongsTo('App\MoUser', 'foreign_key', 'other_key');
    }

    public function lead_address()
    {
        return $this->hasOne('App\Models\lead_addresses');
    }
}
