<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PrCardImage extends Model
{
    use HasFactory;

    protected $table = 'pr_card_images';

    protected $hidden = ['created_at','updated_at'];

    protected $fillable = [
        'name', 'status'
    ];

    public function getNameAttribute($value)
    {
        return asset('storage/'.$value);
    }

    /**
     * The shops that belong to the product.
     */
    public function cardcategories()
    {
        return $this->belongsToMany('App\Models\PrCardCategory','pr_card_category_pr_card_image','pr_card_category_id', 'pr_card_image_id');
    }

    public function pivotCategory() {
        return $this->hasMany('App\Models\PrCardCategoryPrCardImage');
    }


}
