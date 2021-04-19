<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admin_addresses extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id', 'address_type', 'add1', 'add2', 'city_id', 'state_id', 'is_current', 'add_proof', 'add_proof_no', 'add_proof_isu_date', 'pass_expiry_date', 'id_doc_front', 'id_doc_back'
    ];

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }
    public function pincode()
    {
        return $this->belongsTo('App\Models\pincode');
    }
}
