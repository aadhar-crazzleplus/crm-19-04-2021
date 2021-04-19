<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead_vehicles extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_profile_id', 'regn_no', 'regn_dt', 'chasi_no', 'eng_no', 'vh_class_desc', 'maker_desc', 'maker_model', 'body_type_desc', 'fuel_desc', 'fit_upto', 'norms_desc', 'insurance_comp', 'insurance_policy_no', 'insurance_upto', 'registered_at', 'manu_month_yr', 'vch_catg', 'pucc_upto', 'pucc_no', 'financer', 'status_as_on', 'api_result', 'rc_img', 'policy_img', 'policy_type'
    ];

    public function lead_profile()
    {
        return $this->belongsTo('App\Models\lead_profiles','lead_profile_id','id');
    }
}
