<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admin_rel_banks extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_id', 'admin_id', 'name_on_bank', 'ifsc_code', 'branch_name', 'account_no', 'customer_id', 'uploads', 'upload_doc', 'status'
    ];

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }
    public function bank()
    {
        return $this->belongsTo('App\Models\bank');
    }
}
