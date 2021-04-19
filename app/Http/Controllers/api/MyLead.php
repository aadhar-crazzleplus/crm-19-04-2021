<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjectResource;
use App\Models\leads;

class MyLead extends BaseController
{
    public function myleads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $leads = leads::where("lead_by",$request->user_id)->orwhere("assign_to",$request->user_id)->get();
        $lead_arr=array();
        foreach($leads as $lead){
            if($lead->lead_status == "i"){
                $lead_status = "Incomplete";
                $closedDate= "N/A";
            }elseif($lead->lead_status == "p"){
                $lead_status = "Processing";
                $closedDate= "N/A";
            }elseif($lead->lead_status == "c"){
                $lead_status = "Closed";
                $closedDate= $lead->updated_at;
            }elseif($lead->lead_status == "r"){
                $lead_status = "Rejected";
                $closedDate= $lead->updated_at;
            }
            if($lead->product_id == 6){
                if(isset($lead->lead_profile->lead_vehicle->rc_img)
                && $lead->lead_profile->lead_vehicle->rc_img != ""
                && $lead->lead_profile->lead_vehicle->policy_img != ""
                && $lead->lead_profile->pan_img != ""
                && $lead->lead_profile->adhar_img != ""){
                    $is_doc = "Uploaded !!";
                }else{
                    $is_doc = "Not Uploaded !!";
                }
            }else{
                $is_doc = "N/A";
            }


            $lead_arr[]=[
                "lead_id"=>$lead->id,
                "lead_title"=>$lead->product->title,
                "lead_by"=>$lead->leads_by->first_name." ".$lead->leads_by->last_name,
                "assign_to"=>$lead->assigns_to->first_name." ".$lead->assigns_to->last_name,
                "lead_remark"=>$lead->lead_remark,
                "lead_status"=>$lead->lead_status,
                "mobile_no"=>$lead->lead_profile->mobile_no,
                "document"=>$is_doc,
                "created_at"=>$lead->created_at,
                "updated_at"=>$closedDate
            ];
        }
        $data["leads"]=!empty($lead_arr)?$lead_arr:NULL;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
}
