<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rel_bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id', 'user_id', 'name_on_bank', 'ifsc_code', 'branch_name', 'account_no', 'customer_id', 'uploads', 'upload_doc', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function bank()
    {
        return $this->belongsTo('App\Models\bank');
    }
}
