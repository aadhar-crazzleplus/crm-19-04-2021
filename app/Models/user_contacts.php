<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_contacts extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'contact_name', 'mobile_no'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', "user_id", "id");
    }
}
