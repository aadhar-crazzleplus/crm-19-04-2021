<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'content', 'updated_by', 'created_at', 'updated_at'
    ];
    public function updatedBy()
    {
        return $this->belongsTo('App\Models\Admin','updated_by','id');
    }
}
