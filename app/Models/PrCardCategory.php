<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrCardCategory extends Model
{
    use HasFactory;

    protected $table = 'pr_card_categories';

    // protected $hidden = ['created_at','updated_at','status','id'];

    protected $fillable = [
        'name', 'status'
    ];

    /**
     * The images that belong to the category.
     */
    public function cardimages()
    {
        return $this->belongsToMany('App\Models\PrCardImage','pr_card_category_pr_card_image','pr_card_image_id', 'pr_card_category_id')->distinct();
    }
    
}
