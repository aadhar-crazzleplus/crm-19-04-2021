<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead_addresses extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_profile_id', 'address', 'pincode_id', 'city_id', 'state_id', 'is_current', 'add_type', 'cur_add_year', 'cur_add_month'
    ];
    public function lead_profile()
    {
        return $this->belongsTo('App\Models\lead_profiles','lead_profile_id','id');
    }
    public function pincode()
    {
        return $this->belongsTo('App\Models\pincode');
    }
}
