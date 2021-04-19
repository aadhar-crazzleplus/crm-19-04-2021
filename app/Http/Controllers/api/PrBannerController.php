<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProjectResource;
use App\Models\pr_banners;

class PrBannerController extends BaseController
{


    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrBanners(Request $request)
    {
        $prBanners = pr_banners::where("status",'1')->get(["id","image",'url']);
        $data = [];
        foreach($prBanners as $prBanner){
            $img = ($prBanner->image != "")?url("storage/pr_banners/".$prBanner->image):"";
            $data[] = [
                'id' => $prBanner->id,
                'image' => $img,
                'url' => $prBanner->url
            ];
        }
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }


}
