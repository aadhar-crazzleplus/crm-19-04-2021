<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\pincode;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getpincode(Request $request)
    {
        $pincodes = [];
        if($request->has('q')){
            $search = $request->q;
            $pincodes = pincode::select("id", "pincode")
            		->where('pincode', 'LIKE', "%$search%")
            		->get();
        }
        // $pincodes = pincode::all(['id','pincode']);
        return response()->json($pincodes);
    }
    public function getcity(Request $request)
    {
        $cities = city::where("state_id",$request->state_id)->get(['id','city_name']);
        return response()->json($cities);
    }
    public function getcitystate(Request $request)
    {
        $pincode = pincode::find($request->pincode_id);
        // $pincode->city->state_id
        $cities = city::where("state_id",$pincode->city->state_id)->get(['id','city_name','state_id']);
        $data['cities']=$cities;
        $data['citystate_id']=['city_id'=>$pincode->city->id, 'state_id'=>$pincode->city->state_id];
        return response()->json($data);
    }
}
