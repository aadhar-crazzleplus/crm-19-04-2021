<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrCardCategoryPrCardImage extends Model
{
    use HasFactory;

    protected $table = 'pr_card_category_pr_card_image';

    protected $hidden = ['created_at','updated_at'];

    protected $fillable = [
        'pr_card_image_id', 'pr_card_category_id'
    ];

    public function associatedCategories() {
        return $this->hasMany('App\Models\PrCardCategory','id','pr_card_category_id');
    }

    public function associatedImages() {
        return $this->hasMany('App\Models\PrCardImage');
    }

}
