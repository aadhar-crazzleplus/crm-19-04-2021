<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\social_cards;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;

class SocialCardController extends BaseController
{
    public function getPrCards(Request $request)
    {
        $soBanners = social_cards::where("status",'1')
                        ->get(["id","image"]);
        $data = [];
        foreach($soBanners as $soBanner){
            $data[] = [
                'id' => $soBanner->id,
                'image' => ($soBanner->image != "")?url("storage/social_banners/".$soBanner->image):""
            ];
        }
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
}
