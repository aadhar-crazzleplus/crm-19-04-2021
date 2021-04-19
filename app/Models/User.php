<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'first_name', 'mobile_no', 'otp', 'email', 'dob', 'password',
    // ];
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'mobile_no', 'otp', 'marital_status', 'citizenship', 'dob', 'gender', 'father_name', 'mother_name', 'spouse_name', 'nominee_name', 'nominee_relation', 'nominee_dob', 'pan_no', 'upload_pan_no', 'gst_no', 'upload_gst_no', 'dependents', 'qualification', 'upload_qual_doc', 'res_status', 'occupation_id', 'organization_id', 'grade_id', 'busi_type_id', 'profession_id', 'net_mon_incm', 'net_yr_incm', 'gros_mon_incm', 'gros_yr_incm', 'cur_job_year', 'cur_job_month', 'total_ex_yr', 'total_ex_month', 'total_bus_yr', 'total_bus_month', 'office_space', 'pos_licence', 'total_bus_anum', 'card_id', 'parent_id', 'advisor_code', 'are_you', 'lead_by', 'lead_com_by', 'smoker_or_chewer', 'referral_code', 'referred_by', 'user_type', 'user_status', 'email_verified_at', 'password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'current_team_id', 'profile_photo_path, profile_photo, fcm_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function usertype()
    {
        return $this->belongsTo('App\Models\user_type','user_type_id', 'id');
    }

    public function address(){
        return $this->hasOne('App\Models\Address');
    }

    public function relbank()
    {
        return $this->hasOne('App\Models\rel_bank');
    }

    // public function relfinance()
    // {
    //     return $this->hasMany('App\Models\rel_fin');
    // }
    public function leads_by()
    {
        return $this->hasOne('App\Models\leads','lead_by','id');
    }

    public function assigns_to()
    {
        return $this->hasOne('App\Models\leads','assign_to','id');
    }

}
