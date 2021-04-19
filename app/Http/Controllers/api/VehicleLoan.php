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

class VehicleLoan extends BaseController
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

    public function veh_loan_reg(Request $request)
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

    public function veh_loan_details(Request $request)
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


    public function addvehicle_profile(Request $request)
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

        // $pincode = pincode::where("pincode",$request->pincode)->first();
        // if(!$pincode)
        //     return $this->sendError('Pincode Validation.', "Pincode does not exist !!");

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

        $lead = lead_profiles::where("id",$request->profile_id)->first();
        $vehicle=array();
        if(isset($lead->lead_vehicle->id) && $lead->lead_vehicle->id != ""){
            $vehicle['vehicle_id'] = $lead->lead_vehicle->id;
            $vehicle['rc_img'] = ($lead->lead_vehicle->rc_img != "")?url("storage/lead/$request->profile_id/".$lead->lead_vehicle->rc_img):"";
            $vehicle['policy_img'] = ($lead->lead_vehicle->policy_img != "")?url("storage/lead/$request->profile_id/".$lead->lead_vehicle->policy_img):"";
        }
        if(isset($lead->pan_img)){
            $vehicle['pan_img'] = ($lead->pan_img != "")?url("storage/lead/$request->profile_id/".$lead->pan_img):"";
            $vehicle['adhar_img'] = ($lead->adhar_img != "")?url("storage/lead/$request->profile_id/".$lead->adhar_img):"";
        }

        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;
        $data["uploads"] = !empty($vehicle)?$vehicle:NULL;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    // public function addvehicle_detais(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'reg_no' => 'required',
    //         'vehicle_type' => 'required',
    //         'make_model' => 'required',
    //         'manufacture_yr' => 'required',
    //         'profile_id' => 'required',
    //         'lead_id' => 'required'
    //         // 'rc_img' => 'required',
    //         // 'policy_img' => 'required',
    //         // 'profile_id' => 'required',
    //         // 'lead_id' => 'required'
    //     ]);

    //     if($validator->fails()){
    //         return $this->sendError('Validation Error.', $validator->errors());
    //     }
    //     $res_arr = lead_vehicles::where("reg_no",$request->reg_no)->first();

    //     if(isset($res_arr->reg_no) && $res_arr->reg_no != ""){

    //         $res_arr->reg_no = $request->reg_no;
    //         $res_arr->vehicle_type = $request->vehicle_type;
    //         $res_arr->make_model = $request->make_model;
    //         $res_arr->manufacture_yr = $request->manufacture_yr;
    //         $res_arr->update();
    //     }else{

    //         $res_arr = new lead_vehicles();
    //         $res_arr->lead_profile_id = $request->profile_id;
    //         $res_arr->reg_no = $request->reg_no;
    //         $res_arr->vehicle_type = $request->vehicle_type;
    //         $res_arr->make_model = $request->make_model;
    //         $res_arr->manufacture_yr = $request->manufacture_yr;
    //         $res_arr->save();
    //     }

    //     $lead = lead_profiles::where("id",$request->profile_id)->first();
    //     $vehicle=array();
    //     if(isset($res_arr->id) && $res_arr->id != ""){
    //         $vehicle['vehicle_id'] = $res_arr->id;
    //         $vehicle['rc_img'] = ($res_arr->rc_img != "")?url("storage/lead/$request->profile_id/".$res_arr->rc_img):"";
    //         $vehicle['policy_img'] = ($res_arr->policy_img != "")?url("storage/lead/$request->profile_id/".$res_arr->policy_img):"";
    //     }
    //     if(isset($lead->pan_img)){
    //         $vehicle['pan_img'] = ($lead->pan_img != "")?url("storage/lead/$request->profile_id/".$lead->pan_img):"";
    //         $vehicle['adhar_img'] = ($lead->adhar_img != "")?url("storage/lead/$request->profile_id/".$lead->adhar_img):"";
    //     }

    //     $data["lead_id"] = $request->lead_id;
    //     $data["profile_id"] = $request->profile_id;
    //     $data["uploads"] = !empty($vehicle)?$vehicle:NULL;

    //     return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    // }

    public function addvehicle_uploads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'full_name' => 'required',
            // 'mobile_no' => 'required|min:10|max:10',
            // 'dob' => 'required',
            'vehicle_id' => "required",
            'lead_id' => "required",
            'profile_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $path = 'public/lead/'.$request->profile_id;
        $rc_img = "";
        if($request->hasFile('rc_img')){
            $rc_img = substr(str_replace(" ","-",$request->file('rc_img')->getClientOriginalName()),-10);
            $rc_img = time().$rc_img;
            $request->file('rc_img')->storeAs($path, $rc_img);
        }
        $policy_img = "";
        if($request->hasFile('policy_img')){
            $policy_img = substr(str_replace(" ","-",$request->file('policy_img')->getClientOriginalName()),-10);
            $policy_img = time().$policy_img;
            $request->file('policy_img')->storeAs($path, $policy_img);
        }
        $pan_img = "";
        if($request->hasFile('pan_img')){
            $pan_img = substr(str_replace(" ","-",$request->file('pan_img')->getClientOriginalName()),-10);
            $pan_img = time().$pan_img;
            $request->file('pan_img')->storeAs($path, $pan_img);
        }
        $adhar_img = "";
        if($request->hasFile('adhar_img')){
            $adhar_img = substr(str_replace(" ","-",$request->file('adhar_img')->getClientOriginalName()),-10);
            $adhar_img = time().$adhar_img;
            $request->file('adhar_img')->storeAs($path, $adhar_img);
        }

        $vehicle = lead_vehicles::find($request->vehicle_id);
        if($rc_img != "") $vehicle->rc_img = $rc_img;
        if($policy_img != "") $vehicle->policy_img = $policy_img;
        $vehicle->update();
        $lead_pro = lead_profiles::find($request->profile_id);
        if($pan_img != "") $lead_pro->pan_img = $pan_img;
        if($adhar_img != "") $lead_pro->adhar_img = $adhar_img;
        $lead_pro->update();

        $lead = lead_profiles::where("id",$request->profile_id)->first();
        $emp_arr=array();
        if(!empty($lead) && $lead->occupation_id == "1"){
            $emp_arr["occupation_id"]=$lead->occupation_id;
            $emp_arr["occu_title"]=$lead->occupation->occu_title;
            $emp_arr["monthly_salary"]=$lead->monthly_salary;
            $emp_arr["company_vintage"]=$lead->company_vintage;
            $emp_arr["pan_no"]=$lead->pan_no;
        }elseif(!empty($lead) && $lead->occupation_id == "2"){
            $emp_arr["occupation_id"]=$lead->occupation_id;
            $emp_arr["occu_title"]=$lead->occupation->occu_title;
            $emp_arr["itr_amount"]=$lead->itr_amount;
            $emp_arr["busi_vintage"]=$lead->busi_vintage;
            $emp_arr["office_setup"]=$lead->office_setup;
            $emp_arr["pan_no"]=$lead->pan_no;
        }

        $data["profile_id"] = $request->profile_id;
        $data["lead_id"] = $request->lead_id;
        $data["emp_detail"] = !empty($emp_arr)?$emp_arr:NULL;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function addvehicle_emp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'occupation_id' => 'required',
            'pan_no' => 'required',
            'profile_id' => 'required',
            'lead_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $res_arr = lead_profiles::where("id",$request->profile_id)->first();
        if($request->occupation_id == "1"){
            $res_arr->occupation_id=$request->occupation_id;
            $res_arr->monthly_salary=$request->monthly_salary;
            $res_arr->company_vintage=$request->company_vintage;
            $res_arr->pan_no=$request->pan_no;
            $res_arr->update();
        }elseif($request->occupation_id == "2"){
            $res_arr->occupation_id=$request->occupation_id;
            $res_arr->itr_amount=$request->itr_amount;
            $res_arr->busi_vintage=$request->busi_vintage;
            $res_arr->office_setup=$request->office_setup;
            $res_arr->pan_no=$request->pan_no;
            $res_arr->update();
        }else{
            return $this->sendError('Occupation Error.', array("err_msg"=>["Wrong Occupation !!"]));
        }

        $lead = leads::find($request->lead_id);
        $lead->lead_status = 'p';
        $lead->update();

        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function getproducts(Request $request)
    {
        if(isset($request->product_id) && $request->product_id != ""){
            $product = products::where("product_id",$request->product_id)->get(['id','title']);
            return $this->sendResponse(new ProjectResource($product), "Performed successfully");
        }else{
            $product = products::whereNull('product_id')->get(['id','title']);
            return $this->sendResponse(new ProjectResource($product), "Performed successfully");
        }

    }

    public function company_list(Request $request)
    {
        if(isset($request->company_id) && $request->company_id != ""){
            $company = companies::where("id",$request->company_id)->get(['id','company_name']);
            return $this->sendResponse(new ProjectResource($company), "Performed successfully");
        }else{
            $company = companies::all(['id','company_name']);
            return $this->sendResponse(new ProjectResource($company), "Performed successfully");
        }

    }
    public function search_companies(Request $request)
    {
        $company = [];
        if($request->has('q')){
            $search = $request->q;
            $company = companies::select("id", "company_name")->orderBy('company_name')
            		->where('company_name', 'LIKE', "%$search%")
            		->get();
        }
        // $company = companies::all(['id','company_name']);
        return $this->sendResponse(new ProjectResource($company), "Performed successfully");
    }

    public function getoccupations(Request $request)
    {
        $occupation = occupation::all(['id','occu_title']);
        return $this->sendResponse(new ProjectResource($occupation), "Performed successfully");
    }
}
