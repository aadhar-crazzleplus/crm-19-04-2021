<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Arr;
use App\Models\PrBanner;

class PrBannerController extends BaseController
{
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrBanners(Request $request)
    {
        $prBanner = PrBanner::where("status",'1')->get(["id","title","image"])->toArray();
        return $this->sendResponse(new ProjectResource($prBanner), "Performed successfully");
    }
    

}
