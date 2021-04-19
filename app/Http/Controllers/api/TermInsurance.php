<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjectResource;
use App\Models\Address;
use App\Models\bank;
use App\Models\city;
use App\Models\companies;
use App\Models\fin_product;
use App\Models\lead_addresses;
use App\Models\lead_is_cards;
use App\Models\lead_is_loans;
use App\Models\lead_profiles;
use App\Models\lead_vehicles;
use App\Models\leads;
use App\Models\occupation;
use App\Models\pincode;
use App\Models\pos_income;
use App\Models\products;
use App\Models\rel_bank;
use App\Models\rel_fin;
use App\Models\state;
use App\Models\temp_otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use stdClass;

class TermInsurance extends BaseController
{
    public function getotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $otp = rand(1000,9999);
        $sms = sendOtp($otp,$request->mobile_no);
        if(isset($sms->status) && $sms->status == "success"){
            $validatedData["otp"] = isset($sms->otp)?$sms->otp:$otp;
            // $validatedData["otp"] = 1111;
            $validatedData["mobile_no"] = $request->mobile_no;
            $temp = temp_otp::create($validatedData);
            $success['mobile_no'] =  $request->mobile_no;
        }else{
            return $this->sendError('SMS Error.', array("err_msg"=>["SMS sending Error"]));
        }
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");

    }

    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10',
            'otp' => 'required|min:4|max:4'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $otp = temp_otp::where("mobile_no",$request->mobile_no)->where("otp",$request->otp)->first();
        if(!$otp){
            return $this->sendError('OTP Error.', array("err_msg"=>["Wrong OTP !!"]));
        }

        $lead = lead_profiles::where("mobile_no",$request->mobile_no)->first();
        $personal=array();
        if(!empty($lead)){
            $personal["profile_id"]=$lead->id;
            // $personal["lead_by"]=$lead->lead->lead_by;
            $personal["full_name"]=$lead->full_name;
            $personal["mobile_no"]=$lead->mobile_no;
            $personal["email"]=$lead->email;
            $personal["dob"]=$lead->dob;
        }
        $add=array();
        if(isset($lead->lead_address->id)){
            $add['address_id'] = $lead->lead_address->id;
            $add['address'] = $lead->lead_address->address;
            $add['pincode_id'] = $lead->lead_address->pincode_id;
            $add['city_id'] = $lead->lead_address->city_id;
            $add['state_id'] = $lead->lead_address->state_id;
        }

        $data["profile"] = !empty($personal)?$personal:NULL;
        $data["address"] = !empty($add)?$add:NULL;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");

    }

    // private function sendOtp($otp, $moble)
    // {
    //     $apiKey = "jY6vpE92NEinBjox0ThADw";
    //     $api = "http://mysms.msg24.in/api/mt/SendSMS";
    //     $msg = "The OTP is $otp. Please don't share with anyone, generated at ".date("Y-m-d H:i:s")." and Valid for 2 minutes -BankSathi ";
    //     $data = [
    //             "APIKey"=>$apiKey,
    //             "senderid"=>"BSATHI",
    //             "channel"=>"Trans",
    //             "number"=>$moble,
    //             "text"=>$msg,
    //             "route"=>"8",
    //             "DCS"=>"0",
    //             "flashsms"=>"0"
    //         ];
    //     $res = Http::get($api,$data);
    //     return json_decode($res);
    // }

    public function term_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'mobile_no' => 'required|min:10|max:10',
            'dob' => 'required',
            'pincode_id' => "required",
            'lead_by' => "required",
            'product_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if(isset($request->profile_id) && $request->profile_id > 0){
            $lead_pro = lead_profiles::find($request->profile_id);
            if($lead_pro->mobile_no != ""){
                $lead_pro->full_name = $request->full_name;
                $lead_pro->mobile_no = $request->mobile_no;
                $lead_pro->email = $request->email;
                $lead_pro->dob = date('Y-m-d',strtotime($request->dob));
                $lead_pro->update();
            }else{
                return $this->sendError('Profile Validation.', array("err_msg"=>["Profile Id does not exist !!"]));
            }

        }else{
            $lead_pro = new lead_profiles();
            $lead_pro->full_name = $request->full_name;
            $lead_pro->mobile_no = $request->mobile_no;
            $lead_pro->email = $request->email;
            $lead_pro->dob = date('Y-m-d',strtotime($request->dob));
            $lead_pro->save();
        }

        if(isset($request->address_id) && $request->address_id > 0){
            $add = lead_addresses::find($request->address_id);
            if($add->id != ""){
                $add->address = $request->address;
                $add->pincode_id = $request->pincode_id;
                $add->city_id = $request->city_id;
                $add->state_id = $request->state_id;
                // $add->add_type = $request->add_type;
                $add->is_current = $request->is_current??"yes";
                $add->update();
            }else{
                return $this->sendError('Address Validation.', array("err_msg"=>["Address Id does not exist !!"]));
            }

            $lead = leads::where('lead_by', $request->lead_by)
                    ->where('product_id',$request->product_id)
                    ->where('lead_profile_id',$lead_pro->id)->first();
            if(!$lead){
                $lead = new leads();
                $lead->lead_by = $request->lead_by;
                $lead->assign_to = $request->lead_by;
                $lead->close_by = $request->lead_by;
                $lead->product_id = $request->product_id;
                $lead->lead_profile_id = $lead_pro->id;
                $lead->lead_status = 'i';
                $lead->save();
            }else{
                $lead->lead_by = $request->lead_by;
                $lead->assign_to = $request->lead_by;
                $lead->close_by = $request->lead_by;
                $lead->product_id = $request->product_id;
                $lead->lead_profile_id = $lead_pro->id;
                $lead->lead_status = 'i';
                $lead->update();
            }

        }else{
            $add = new lead_addresses();
            $add->lead_profile_id = $lead_pro->id;
            $add->address = $request->address;
            $add->pincode_id = $request->pincode_id;
            $add->city_id = $request->city_id;
            $add->state_id = $request->state_id;
            // $add->add_type = $request->add_type;
            $add->is_current = $request->is_current??"yes";
            $add->save();

            $lead = new leads();
            $lead->lead_by = $request->lead_by;
            $lead->assign_to = $request->lead_by;
            $lead->close_by = $request->lead_by;
            $lead->product_id = $request->product_id;
            $lead->lead_profile_id = $lead_pro->id;
            $lead->lead_status = 'i';
            $lead->save();
        }


        temp_otp::where("mobile_no", $request->mobile_no)->delete();
        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;
        $data["web_view_url"] = "https://insurance.kotak.com/OnlineTermInsurance/?source=BANKSATHI";

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
}
