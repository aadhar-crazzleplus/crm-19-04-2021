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

class VehicleInsurance extends BaseController
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

    public function veh_reg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10',
            'regn_no' => 'required',
            'lead_by' => "required",
            'product_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $return=array();
        $lead_veh = lead_vehicles::where("regn_no",trim($request->regn_no))->first();
        if(!$lead_veh){

            $api_res = get_veh_api(trim($request->regn_no));
            $api = json_decode($api_res);
            if(isset($api->statusCode) && isset($api->message)){
                return $this->sendError('API Validation.', array("err_msg"=>[$api->message]));
            }

            if(isset($api->result) && !empty($api->result) && $api->response_msg == "Success"){
                $data = $api->result;
                $lead_pro = lead_profiles::where("mobile_no",$request->mobile_no)->first();
                if(!$lead_pro){
                    $lead_pro = new lead_profiles();
                    $lead_pro->full_name = $data->rc_owner_name;
                    $lead_pro->mobile_no = $request->mobile_no;
                    $lead_pro->save();

                    $pincode = pincode::where("pincode",substr($data->rc_present_address,-6))->first();
                    if(!$pincode)
                        $pin_id = NULL;
                    else $pin_id = $pincode->id;
                    $add = new lead_addresses();
                    $add->lead_profile_id = $lead_pro->id;
                    $add->address = $data->rc_present_address;
                    $add->pincode_id = $pin_id;
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

                    $veh = new lead_vehicles();
                    $veh->lead_profile_id = $lead_pro->id;
                    $veh->regn_no = $data->rc_regn_no;
                    $veh->regn_dt = date("Y-m-d",strtotime($data->rc_regn_dt));
                    $veh->chasi_no = $data->rc_chasi_no;
                    $veh->eng_no = $data->rc_eng_no;
                    $veh->vh_class_desc = $data->rc_vh_class_desc;
                    $veh->maker_desc = $data->rc_maker_desc;
                    $veh->maker_model = $data->rc_maker_model;
                    $veh->body_type_desc = $data->rc_body_type_desc;
                    $veh->fuel_desc = $data->rc_fuel_desc;
                    $veh->fit_upto = date("Y-m-d",strtotime($data->rc_fit_upto));
                    $veh->norms_desc = $data->rc_norms_desc;
                    $veh->insurance_comp = $data->rc_insurance_comp;
                    $veh->insurance_policy_no = $data->rc_insurance_policy_no;
                    $veh->insurance_upto = date("Y-m-d",strtotime($data->rc_insurance_upto));
                    $veh->registered_at = $data->rc_registered_at;
                    $veh->manu_month_yr = $data->rc_manu_month_yr;
                    $veh->owner_sr = $data->rc_owner_sr;
                    $veh->vch_catg = $data->rc_vch_catg;
                    $veh->pucc_upto = date("Y-m-d",strtotime($data->rc_pucc_upto));
                    $veh->pucc_no = $data->rc_pucc_no;
                    $veh->financer = $data->rc_financer;
                    $veh->status_as_on = date("Y-m-d",strtotime($data->rc_status_as_on));
                    $veh->api_result = $api_res;
                    $veh->save();

                    $return["profile_id"] = $lead_pro->id;
                    $return["address_id"] = $add->id;
                    $return["lead_id"] = $lead->id;
                    $return["vehicle_id"] = $veh->id;
                    $return["maker_desc"] = $data->rc_maker_desc;
                    $return["maker_model"] = $data->rc_maker_model;
                    $return["fuel_desc"] = $data->rc_fuel_desc;
                    $return["registered_at"] = $data->rc_registered_at;
                    $return["regn_dt"] = date("Y-m-d",strtotime($data->rc_regn_dt));
                    $return["insurance_upto"] = date("Y-m-d",strtotime($data->rc_insurance_upto));

                }else{
                    $lead_pros = lead_profiles::find($lead_pro->id);
                    $lead_pros->full_name = $data->rc_owner_name;
                    $lead_pros->update();

                    $pincode = pincode::where("pincode",substr($data->rc_present_address,-6))->first();
                    if(!$pincode)
                        $pin_id = NULL;
                    else $pin_id = $pincode->id;
                    $add = lead_addresses::find($lead_pro->lead_address->id);
                    $add->lead_profile_id = $lead_pro->id;
                    $add->address = $data->rc_present_address;
                    $add->pincode_id = $pin_id;
                    // $add->add_type = $request->add_type;
                    $add->is_current = $request->is_current??"yes";
                    $add->update();

                    $lead = new leads();
                    $lead->lead_by = $request->lead_by;
                    $lead->assign_to = $request->lead_by;
                    $lead->close_by = $request->lead_by;
                    $lead->product_id = $request->product_id;
                    $lead->lead_profile_id = $lead_pro->id;
                    $lead->lead_status = 'i';
                    $lead->save();

                    $veh = new lead_vehicles();
                    $veh->lead_profile_id = $lead_pro->id;
                    $veh->regn_no = $data->rc_regn_no;
                    $veh->regn_dt = date("Y-m-d",strtotime($data->rc_regn_dt));
                    $veh->chasi_no = $data->rc_chasi_no;
                    $veh->eng_no = $data->rc_eng_no;
                    $veh->vh_class_desc = $data->rc_vh_class_desc;
                    $veh->maker_desc = $data->rc_maker_desc;
                    $veh->maker_model = $data->rc_maker_model;
                    $veh->body_type_desc = $data->rc_body_type_desc;
                    $veh->fuel_desc = $data->rc_fuel_desc;
                    $veh->fit_upto = date("Y-m-d",strtotime($data->rc_fit_upto));
                    $veh->norms_desc = $data->rc_norms_desc;
                    $veh->insurance_comp = $data->rc_insurance_comp;
                    $veh->insurance_policy_no = $data->rc_insurance_policy_no;
                    $veh->insurance_upto = date("Y-m-d",strtotime($data->rc_insurance_upto));
                    $veh->registered_at = $data->rc_registered_at;
                    $veh->manu_month_yr = $data->rc_manu_month_yr;
                    $veh->owner_sr = $data->rc_owner_sr;
                    $veh->vch_catg = $data->rc_vch_catg;
                    $veh->pucc_upto = date("Y-m-d",strtotime($data->rc_pucc_upto));
                    $veh->pucc_no = $data->rc_pucc_no;
                    $veh->financer = $data->rc_financer;
                    $veh->status_as_on = date("Y-m-d",strtotime($data->rc_status_as_on));
                    $veh->api_result = $api_res;
                    $veh->save();

                    $return["profile_id"] = $lead_pro->id;
                    // $return["address_id"] = $lead_pro->lead_address->id;
                    $return["address_id"] = $add->id;
                    $return["lead_id"] = $lead->id;
                    $return["vehicle_id"] = $veh->id;
                    $return["maker_desc"] = $data->rc_maker_desc;
                    $return["maker_model"] = $data->rc_maker_model;
                    $return["fuel_desc"] = $data->rc_fuel_desc;
                    $return["registered_at"] = $data->rc_registered_at;
                    $return["regn_dt"] = date("Y-m-d",strtotime($data->rc_regn_dt));
                    $return["insurance_upto"] = date("Y-m-d",strtotime($data->rc_insurance_upto));

                }


            }else{
                return $this->sendError('API Validation.', array("err_msg"=>[$api->response_msg]));
            }

        }else{
            $lead = leads::where("lead_profile_id",$lead_veh->lead_profile_id)
                    ->where("product_id",$request->product_id)->first();
                    // ->whereBetween('created_at',[$start_date,$end_date])->first();
            if(!$lead){
                $lead = new leads();
                $lead->lead_by = $request->lead_by;
                $lead->assign_to = $request->lead_by;
                $lead->close_by = $request->lead_by;
                $lead->product_id = $request->product_id;
                $lead->lead_profile_id = $lead_veh->lead_profile_id;
                $lead->lead_status = 'i';
                $lead->save();
            }
            $return["profile_id"] = $lead_veh->lead_profile_id;
            $return["address_id"] = $lead_veh->lead_profile->lead_address->id;
            $return["lead_id"] = $lead->id;
            $return["vehicle_id"] = $lead_veh->id;
            $return["maker_desc"] = $lead_veh->maker_desc;
            $return["maker_model"] = $lead_veh->maker_model;
            $return["fuel_desc"] = $lead_veh->fuel_desc;
            $return["registered_at"] = $lead_veh->registered_at;
            $return["regn_dt"] = date("Y-m-d",strtotime($lead_veh->regn_dt));
            $return["insurance_upto"] = date("Y-m-d",strtotime($lead_veh->insurance_upto));
        }

        temp_otp::where("mobile_no", $request->mobile_no)->delete();

        return $this->sendResponse(new ProjectResource($return), "Performed successfully");

    }

    public function veh_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required',
            'address_id' => 'required',
            'lead_id' => 'required',
            'vehicle_id' => 'required',
            'maker_desc' => 'required',
            'maker_model' => 'required',
            'fuel_desc' => 'required',
            'registered_at' => 'required',
            'regn_dt' => 'required',
            'insurance_upto' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $return=array();
        $veh = lead_vehicles::where("id",$request->vehicle_id)->first();
        $veh->lead_profile_id = $request->profile_id;
        $veh->regn_dt = date("Y-m-d",strtotime($request->regn_dt));
        $veh->maker_desc = $request->maker_desc;
        $veh->maker_model = $request->maker_model;
        $veh->fuel_desc = $request->fuel_desc;
        $veh->insurance_upto = date("Y-m-d",strtotime($request->insurance_upto));
        $veh->registered_at = $request->registered_at;
        $veh->update();

        $lead = $veh->lead_profile;
        $personal=array();
        if(!empty($lead)){
            $personal["profile_id"]=$lead->id;
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

        $return["profile"] = !empty($personal)?$personal:NULL;
        $return["address"] = !empty($add)?$add:NULL;
        return $this->sendResponse(new ProjectResource($return), "Performed successfully");
    }

    public function veh_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'mobile_no' => 'required|min:10|max:10',
            'dob' => 'required',
            'pincode_id' => "required",
            'city_id' => "required",
            'state_id' => "required",
            'address' => "required",
            'address_id' => "required",
            'lead_id' => "required",
            'profile_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

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
        $add = lead_addresses::find($request->address_id);
        if($add->id != ""){
            $add->address = $request->address;
            $add->pincode_id = $request->pincode_id;
            $add->city_id = $request->city_id;
            $add->state_id = $request->state_id;
            $add->is_current = $request->is_current??"yes";
            $add->update();
        }else{
            return $this->sendError('Address Validation.', array("err_msg"=>["Address Id does not exist !!"]));
        }

        $lead = leads::find($request->lead_id);
        $lead->lead_status = 'p';
        $lead->update();

        $data["profile_id"] = $lead_pro->id;
        $data["lead_id"] = $lead->id;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
}
