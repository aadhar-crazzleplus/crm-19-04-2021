<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guard = 'admin';

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'mobile_no', 'otp', 'email_otp', 'marital_status', 'citizenship', 'dob', 'gender', 'father_name', 'mother_name', 'spouse_name', 'nominee_name', 'nominee_relation', 'nominee_dob', 'pan_no', 'upload_pan_no', 'gst_no', 'upload_gst_no', 'dependents', 'qualification', 'upload_qual_doc', 'res_status', 'occupation_id', 'organization_id', 'grade_id', 'busi_type_id', 'profession_id', 'net_mon_incm', 'net_yr_incm', 'gros_mon_incm', 'gros_yr_incm', 'cur_job_year', 'cur_job_month', 'total_ex_yr', 'total_ex_month', 'total_bus_yr', 'total_bus_month', 'office_space', 'pos_licence', 'total_bus_anum', 'verified_by', 'user_code', 'are_you', 'firm_name', 'user_type_id', 'user_status', 'profile_photo', 'password'
    ];

    // public function user_type()
    // {
    //     return $this->hasOne('App\Models\leads','lead_by','id');
    // }

    public function usertype()
    {
        return $this->belongsTo('App\Models\user_type','user_type_id', 'id');
    }

    public function address(){
        return $this->hasOne('App\Models\admin_addresses');
    }

    public function relbank()
    {
        return $this->hasOne('App\Models\admin_rel_banks');
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addNew($input)
    {
        $check = static::where('twitter_id',$input['twitter_id'])->first();

        if(is_null($check)){
            return static::create($input);
        }

        return $check;
    }
}
