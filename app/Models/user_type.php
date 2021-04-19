<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'title','admin_access'
    ];

    public function admins()
    {
        return $this->hasMany('App\Models\Admin','user_type_id', 'id');
    }
    public function users()
    {
        return $this->hasMany('App\Models\User','user_type_id', 'id');
    }
}
