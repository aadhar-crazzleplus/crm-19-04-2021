<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PrCardImage;
use App\Models\PrCardCategory;
use App\Models\PrCardCategoryPrCardImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Arr;


class PrCardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrCards(Request $request)
    {
        $prCardImage = PrCardImage::where("status",'1')
                        ->with(array('pivotCategory.associatedCategories'=>function($query){
                            $query->select('id','name');
                        }))
                        ->get(["title","name","id"])->toArray();

        $myArr=[];
        foreach ($prCardImage as $k1 => $v1) {
            foreach ($v1['pivot_category'] as $k2 => $v2) {
                $myArr[$v1['id']]['cat'] = $v2['associated_categories'][0]['name'];
                $myArr[$v1['id']]['img'] = $v1['name'];
            }
        }
   
        return $this->sendResponse(new ProjectResource($myArr), "Performed successfully");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
