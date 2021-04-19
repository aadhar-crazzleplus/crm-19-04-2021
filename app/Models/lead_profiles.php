<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead_profiles extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name', 'mobile_mo', 'otp', 'email', 'dob', 'occupation_id', 'monthly_salary', 'company_id', 'designation', 'company_vintage', 'office_email', 'itr_amount', 'gst_no', 'gst_vintage', 'pan_no', 'pan_img', 'adhar_no', 'adhar_img', 'busi_vintage', 'office_setup'
    ];

    public function lead_address(){
        return $this->hasOne('App\Models\lead_addresses','lead_profile_id','id');
    }

    public function lead(){
        return $this->hasOne('App\Models\leads','lead_profile_id','id');
    }

    public function company(){
        return $this->belongsTo('App\Models\companies','company_id','id');
    }

    public function occupation(){
        return $this->belongsTo('App\Models\occupation','occupation_id','id');
    }

    public function lead_vehicle(){
        return $this->hasOne('App\Models\lead_vehicles','lead_profile_id','id');
    }

}
