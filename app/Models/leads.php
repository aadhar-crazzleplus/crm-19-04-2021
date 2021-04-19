<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leads extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_by', 'assign_to', 'close_by', 'lead_profile_id', 'product_id', 'lead_remark', 'lead_status'
    ];

    public function lead_profile()
    {
        return $this->belongsTo('App\Models\lead_profiles','lead_profile_id', 'id');
    }

    public function leads_by()
    {
        return $this->belongsTo('App\Models\User','lead_by','id');
    }

    public function assigns_to()
    {
        return $this->belongsTo('App\Models\User','assign_to','id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\products','product_id','id');
    }

    
}
