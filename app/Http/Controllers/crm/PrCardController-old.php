<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Models\PrCard;
use App\Models\PrCardCategory;
use Illuminate\Http\Request;

class PrCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['prCardCategory'] = PrCardCategory::where("status",'1')->get(["name","id"]);
        return view('crm.pr_cards.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        print_r($request->all());
        die();
        // $upload_qual_doc = "";
        // $path = 'public/admins/'.$user_id;
        // if($request->hasFile('upload_qual_doc')){
        //     $upload_qual_doc = substr(str_replace(" ","-",$request->file('upload_qual_doc')->getClientOriginalName()),-10);
        //     $upload_qual_doc = time().$upload_qual_doc;
        //     $request->file('upload_qual_doc')->storeAs($path, $upload_qual_doc);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrCard  $prCard
     * @return \Illuminate\Http\Response
     */
    public function show(PrCard $prCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PrCard  $prCard
     * @return \Illuminate\Http\Response
     */
    public function edit(PrCard $prCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrCard  $prCard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrCard $prCard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrCard  $prCard
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrCard $prCard)
    {
        //
    }
}
