<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjectResource;
use App\Models\lead_addresses;
use App\Models\lead_profiles;
use App\Models\lead_vehicles;
use App\Models\leads;
use App\Models\pincode;
use App\Models\state;
use App\Models\companies;
use App\Models\occupation;
use stdClass;

class VehicleLoan extends BaseController
{

    public function veh_list_loan(){
        // $leads = leads::where("product_id","9")->first();
        // echo $leads->vehicle->regn_no;
        // die;
        $data['occupations'] = occupation::all(['id','occu_title']);
        $data['states'] = state::where("country_id",'1')->get(["state_name","id"]);
        return view("crm.veh_list_loan",$data);
    }

    public function veh_reg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10',
            'regn_no' => 'required',
            'lead_by' => "required",
            'assign_to' => "required",
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);
        }
        $admin_by=auth("admin")->user()->id;
        $product_id = "6";
        $return=array();
        $lead_veh = lead_vehicles::where("regn_no",trim($request->regn_no))->first();
        if(!$lead_veh){
            $api_res = get_veh_api(trim($request->regn_no));
            $api = json_decode($api_res);
            if(isset($api->statusCode) && isset($api->message)){
                return $this->sendError('API Validation.', array("err_msg"=>[$api->message]),200);
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
                    $lead->admin_by = $admin_by;
                    $lead->lead_by = $request->lead_by;
                    $lead->assign_to = $request->assign_to;
                    $lead->close_by = $request->assign_to;
                    $lead->updated_by = $admin_by;
                    $lead->product_id = $product_id;
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
                    $lead->admin_by = $admin_by;
                    $lead->lead_by = $request->lead_by;
                    $lead->assign_to = $request->assign_to;
                    $lead->close_by = $request->assign_to;
                    $lead->updated_by = $admin_by;
                    $lead->product_id = $product_id;
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
                    $return["regn_dt"] = date("Y",strtotime($data->rc_regn_dt));
                    $return["insurance_upto"] = date("d-m-Y",strtotime($data->rc_insurance_upto));

                }


            }else{
                return $this->sendError('API Validation.', array("err_msg"=>[$api->response_msg]),200);
            }

        }else{
            $lead = leads::where('lead_by', $request->lead_by)
                    ->where("lead_profile_id",$lead_veh->lead_profile_id)
                    ->where("product_id",$product_id)->first();
                    // ->whereBetween('created_at',[$start_date,$end_date])->first();$lead_veh
            if(isset($lead->id) && $lead->id > 0){
                $lead->lead_by = $request->lead_by;
                $lead->assign_to = $request->assign_to;
                $lead->close_by = $request->assign_to;
                $lead->updated_by = $admin_by;
                $lead->lead_status = 'i';
                $lead->update();
            }else{
                $lead = new leads();
                $lead->admin_by = $admin_by;
                $lead->lead_by = $request->lead_by;
                $lead->assign_to = $request->assign_to;
                $lead->close_by = $request->assign_to;
                $lead->updated_by = $admin_by;
                $lead->product_id = $product_id;
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
            $return["regn_dt"] = date("Y",strtotime($lead_veh->regn_dt));
            $return["insurance_upto"] = date("d-m-Y",strtotime($lead_veh->insurance_upto));
        }

        // temp_otp::where("mobile_no", $request->mobile_no)->delete();

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
        $veh->regn_dt = date("Y-m-d",strtotime($request->regn_dt."-".date("m-d",strtotime(date($veh->regn_dt)))));
        $veh->maker_desc = $request->maker_desc;
        $veh->maker_model = $request->maker_model;
        $veh->fuel_desc = $request->fuel_desc;
        $veh->insurance_upto = date("Y-m-d",strtotime($request->insurance_upto));
        $veh->registered_at = $request->registered_at;
        $veh->update();

        $lead = $veh->lead_profile;
        $personal=array();
        if(!empty($lead)){
            $personal["lead_id"]=$request->lead_id;
            $personal["profile_id"]=$lead->id;
            $personal["full_name"]=$lead->full_name;
            $personal["mobile_no"]=$lead->mobile_no;
            $personal["email"]=$lead->email;
            $personal["dob"]=date("d-m-Y",strtotime($lead->dob));
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
            'pincodes_id' => "required",
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
            $add->pincode_id = $request->pincodes_id;
            $add->city_id = $request->city_id;
            $add->state_id = $request->state_id;
            $add->is_current = $request->is_current??"yes";
            $add->update();
        }else{
            return $this->sendError('Address Validation.', array("err_msg"=>["Address Id does not exist !!"]));
        }

        // $lead = lead_profiles::where("id",$request->profile_id)->first();
        // $vehicle=array();
        if(isset($lead_pro->lead_vehicle->id) && $lead_pro->lead_vehicle->id != ""){
            $vehicle['vehicle_id'] = $lead_pro->lead_vehicle->id;
            $vehicle['rc_img'] = ($lead_pro->lead_vehicle->rc_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->lead_vehicle->rc_img):"";
            $vehicle['policy_img'] = ($lead_pro->lead_vehicle->policy_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->lead_vehicle->policy_img):"";
        }
        $vehicle['pan_img'] = ($lead_pro->pan_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->pan_img):"";
        $vehicle['adhar_img'] = ($lead_pro->adhar_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->adhar_img):"";

        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;
        $data["uploads"] = !empty($vehicle)?$vehicle:NULL;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }


    public function veh_uploads_loan(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

    public function veh_emp_loan(Request $request)
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

    public function getleads(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $user = auth("admin")->user();

        // Total records
        $records = array();
        $product_id = 6;
        // $leads = leads::where("product_id","9")->get();
        $totalRecords = leads::select('count(*) as allcount')
                            ->where("product_id",$product_id)->count();

       $totalRecordswithFilter = leads::select('count(*) as allcount')
                            ->where(function($query) use ($searchValue){
                                $query->where('lead_status', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhereIn('lead_profile_id',function($veh_query) use ($searchValue){
                                        $veh_query->select('lead_profile_id')->from('lead_vehicles')
                                        ->where('regn_no', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('maker_desc', 'LIKE', '%'.$searchValue.'%');
                                     })
                                    ->orWhereIn('assign_to',function($veh_query) use ($searchValue){
                                        $veh_query->select('id')->from('users')
                                        ->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%');
                                     })
                                    ->orWhereIn('lead_profile_id',function($veh_query) use ($searchValue){
                                        $veh_query->select('id')->from('lead_profiles')
                                        ->where('full_name', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('mobile_no', 'LIKE', '%'.$searchValue.'%');
                                    });
                            })
                            ->where("lead_status","<>","d")
                            ->where("product_id",$product_id)->count();
        // Fetch records
        $leads = leads::orderBy($columnName,$columnSortOrder)
                    ->where(function($query) use ($searchValue){
                        $query->where('lead_status', 'LIKE', '%'.$searchValue.'%')
                            ->orWhereIn('lead_profile_id',function($veh_query) use ($searchValue){
                                $veh_query->select('lead_profile_id')->from('lead_vehicles')
                                ->where('regn_no', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('maker_desc', 'LIKE', '%'.$searchValue.'%');
                            })
                            ->orWhereIn('assign_to',function($veh_query) use ($searchValue){
                                $veh_query->select('id')->from('users')
                                ->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%');
                            })
                            ->orWhereIn('lead_profile_id',function($veh_query) use ($searchValue){
                                $veh_query->select('id')->from('lead_profiles')
                                ->where('full_name', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('mobile_no', 'LIKE', '%'.$searchValue.'%');
                            });
                    })
                    ->where("lead_status","<>","d")
                    ->where("product_id",$product_id)
                    // ->select('users.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();

        $lead_arr=array();
        if(!empty($leads))
        foreach($leads as $lead){
            if($lead->lead_status == "i"){
                $lead_status = "<span class='text-warning'>Incomplete</span>";
                $closedDate= $lead->updated_at;
            }elseif($lead->lead_status == "p"){
                $lead_status = "<span class='text-primary'>Processing</span>";
                $closedDate= $lead->updated_at;
            }elseif($lead->lead_status == "c"){
                $lead_status = "<span class='text-success'>Closed</span>";
                $closedDate= $lead->updated_at;
            }elseif($lead->lead_status == "r"){
                $lead_status = "<span class='text-danger'>Rejected</span>";
                $closedDate= $lead->updated_at;
            }

            $edit = route('veh-edit-loan',$lead->id);
            $delete = route('veh-del-loan',$lead->id);
            $action = '<a data-toggle="modal" data-target="#editModal2" href="#editModal2" onclick="editLead(\''.$edit.'\');">Edit</a> | <a href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';

            $lead_arr[]=[
                // "lead_id"=>$lead->id,
                "full_name"=>'<a data-toggle="modal" data-target="#showModal3" href="#showModal3" onclick="showLead(\''.$lead->id.'\');">'.$lead->lead_profile->full_name.'</a>',
                "regn_no"=>$lead->vehicle->regn_no,
                "assign_to"=>$lead->assigns_to->first_name." ".$lead->assigns_to->last_name,
                "mobile_no"=>$lead->lead_profile->mobile_no,
                "maker_desc"=>$lead->vehicle->maker_desc,
                "lead_status"=>$lead_status,
                "updated_at"=>date("d-m-Y H:i:s",strtotime($closedDate)),
                "action" => $action,
            ];
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $lead_arr
        );

        return response()->json($response);
    }

    public function veh_edit_loan($id){
        $lead = leads::find($id);
        $data["lead_id"] = $lead->id;
        $data["lead_status"] = $lead->lead_status;
        $data["lead_remark"] = $lead->lead_remark;
        $data["profile_id"] = $lead->lead_profile_id;
        $data["address_id"] = $lead->lead_address->id;
        $data["vehicle_id"] = $lead->vehicle->id;
        $data["maker_desc"] = $lead->vehicle->maker_desc;
        $data["maker_model"] = $lead->vehicle->maker_model;
        $data["fuel_desc"] = $lead->vehicle->fuel_desc;
        $data["registered_at"] = $lead->vehicle->registered_at;
        $data["regn_dt"] = date("Y",strtotime($lead->vehicle->regn_dt));
        $data["insurance_upto"] = $lead->vehicle->insurance_upto;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function veh_show_loan(Request $request){
        $lead = leads::find($request->id);
        if($lead->lead_status == "i"){
            $lead_status = "<span class='text-warning'>Incomplete</span>";
            $closedDate= $lead->updated_at;
        }elseif($lead->lead_status == "p"){
            $lead_status = "<span class='text-primary'>Processing</span>";
            $closedDate= $lead->updated_at;
        }elseif($lead->lead_status == "c"){
            $lead_status = "<span class='text-success'>Closed</span>";
            $closedDate= $lead->updated_at;
        }elseif($lead->lead_status == "r"){
            $lead_status = "<span class='text-danger'>Rejected</span>";
            $closedDate= $lead->updated_at;
        }
        $data["product_title"] = $lead->product->title;
        $data["lead_id"] = $lead->id;
        $data["lead_status"] = $lead_status;
        $data["lead_remark"] = $lead->lead_remark;
        $data["lead_by"] = $lead->leads_by->first_name." ".$lead->leads_by->last_name;
        $data["assign_to"] = $lead->assigns_to->first_name." ".$lead->assigns_to->last_name;
        $updatedBy = isset($lead->updateds_by->first_name)?$lead->updateds_by->first_name." ".$lead->updateds_by->last_name:"";
        $data["updated_by"] = $updatedBy;

        $data["full_name"] = $lead->lead_profile->full_name;
        $data["mobile_no"] = $lead->lead_profile->mobile_no;
        $data["email"] = $lead->lead_profile->email;
        $data["dob"] = date("d-m-Y",strtotime($lead->lead_profile->dob));
        $data["address"] = $lead->lead_address->address;
        $data["pincode"] = $lead->lead_address->pincode->pincode??NULL;
        $data["city"] = $lead->lead_address->city->city_name??NULL;
        $data["state"] = $lead->lead_address->state->state_name??NULL;

        $data["regn_no"] = $lead->vehicle->regn_no;
        $data["maker_desc"] = $lead->vehicle->maker_desc;
        $data["maker_model"] = $lead->vehicle->maker_model;
        $data["vh_class_desc"] = $lead->vehicle->vh_class_desc;
        $data["body_type_desc"] = $lead->vehicle->body_type_desc;
        $data["fuel_desc"] = $lead->vehicle->fuel_desc;
        $data["fit_upto"] = date("d-m-Y",strtotime($lead->vehicle->fit_upto));
        $data["insurance_comp"] = $lead->vehicle->insurance_comp;
        $data["insurance_policy_no"] = $lead->vehicle->insurance_policy_no;
        $data["registered_at"] = $lead->vehicle->registered_at;
        $data["regn_dt"] = date("Y",strtotime($lead->vehicle->regn_dt));
        $data["insurance_upto"] = date("d-m-Y",strtotime($lead->vehicle->insurance_upto));
        $data["manu_month_yr"] = $lead->vehicle->manu_month_yr;
        $data["owner_sr"] = $lead->vehicle->owner_sr;
        $data["financer"] = $lead->vehicle->financer;
        $data["status_as_on"] = date("d-m-Y",strtotime($lead->vehicle->status_as_on));

        $data["occupation_id"] = $lead->lead_profile->occupation_id;
        if($lead->lead_profile->occupation_id == "2"){
            $data["occu_title"] = $lead->lead_profile->occupation->occu_title;
            $data["itr_amount"] = $lead->lead_profile->itr_amount;
            $data["busi_vintage"] = $lead->lead_profile->busi_vintage;
            $data["office_setup"] = $lead->lead_profile->office_setup;
            $data["monthly_salary"] = "";
            $data["company_vintage"] = "";
            $data["pan_no"] = $lead->lead_profile->pan_no;
        }else{
            $data["occu_title"] = $lead->lead_profile->occupation->occu_title??NULL;
            $data["itr_amount"] = "";
            $data["busi_vintage"] = "";
            $data["office_setup"] = "";
            $data["monthly_salary"] = $lead->lead_profile->monthly_salary;
            $data["company_vintage"] = $lead->lead_profile->company_vintage;
            $data["pan_no"] = $lead->lead_profile->pan_no;
        }

        $data["rc_img"] = url("storage/lead/$lead->lead_profile_id/".$lead->vehicle->rc_img);
        $data["policy_img"] = url("storage/lead/$lead->lead_profile_id/".$lead->vehicle->policy_img);
        $data["pan_img"] = url("storage/lead/$lead->lead_profile_id/".$lead->lead_profile->pan_img);
        $data["adhar_img"] = url("storage/lead/$lead->lead_profile_id/".$lead->lead_profile->adhar_img);

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
    public function veh_update_veh_loan(Request $request){
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


        $vehicle = lead_vehicles::find($request->vehicle_id);
        $vehicle->maker_desc = $request->maker_desc;
        $vehicle->maker_model = $request->maker_model;
        $vehicle->fuel_desc = $request->fuel_desc;
        $vehicle->registered_at = $request->registered_at;
        $vehicle->regn_dt = date("Y-m-d",strtotime($request->regn_dt."-".date("m-d",strtotime(date($vehicle->regn_dt)))));
        $vehicle->insurance_upto = $request->insurance_upto;
        $vehicle->update();

        $data["profile_id"] = $vehicle->lead_profile_id;
        $data["address_id"] = $vehicle->lead_address->id;
        $data["lead_id"] = $request->lead_id;
        $data["lead_status"] = $request->lead_status;
        $data["lead_remark"] = $request->lead_remark;
        $data["full_name"] = $vehicle->lead_profile->full_name;
        $data["mobile_no"] = $vehicle->lead_profile->mobile_no;
        $data["email"] = $vehicle->lead_profile->email;
        $data["dob"] = date("d-m-Y",strtotime($vehicle->lead_profile->dob));
        $data["address"] = $vehicle->lead_address->address;
        $data["pincode_id"] = $vehicle->lead_address->pincode_id;
        $data["city_id"] = $vehicle->lead_address->city_id;
        $data["state_id"] = $vehicle->lead_address->state_id;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
    public function veh_update_pro_loan(Request $request){

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'mobile_no' => 'required|min:10|max:10',
            'dob' => 'required',
            'pincodes_id' => "required",
            'city_id' => "required",
            'state_id' => "required",
            'address' => "required",
            'address_id' => "required",
            'lead_id' => "required",
            'profile_id' => "required",
            // 'lead_status' => "required",
            // 'lead_remark' => "required",
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $lead_pro = lead_profiles::find($request->profile_id);
        $lead_pro->full_name = $request->full_name;
        $lead_pro->mobile_no = $request->mobile_no;
        $lead_pro->dob = date("Y-m-d",strtotime($request->dob));
        $lead_pro->update();

        $add = lead_addresses::find($request->address_id);
        $add->pincode_id = $request->pincodes_id;
        $add->city_id = $request->city_id;
        $add->state_id = $request->state_id;
        $add->address = $request->address;
        $add->update();

        if(isset($lead_pro->lead_vehicle->id) && $lead_pro->lead_vehicle->id != ""){
            $vehicle['vehicle_id'] = $lead_pro->lead_vehicle->id;
            $vehicle['rc_img'] = ($lead_pro->lead_vehicle->rc_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->lead_vehicle->rc_img):"";
            $vehicle['policy_img'] = ($lead_pro->lead_vehicle->policy_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->lead_vehicle->policy_img):"";
        }
        $vehicle['pan_img'] = ($lead_pro->pan_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->pan_img):"";
        $vehicle['adhar_img'] = ($lead_pro->adhar_img != "")?url("storage/lead/$request->profile_id/".$lead_pro->adhar_img):"";

        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;
        $data["uploads"] = !empty($vehicle)?$vehicle:NULL;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function veh_update_uploads_loan(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        $emp_arr["occupation_id"]="1";
        $emp_arr["occu_title"]="";
        $emp_arr["monthly_salary"]="";
        $emp_arr["company_vintage"]="Less Then 1 Year";
        $emp_arr["pan_no"]="";

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
        $lead = leads::find($request->lead_id);
        $data["lead_status"] = $lead->lead_status;
        $data["lead_remark"] = $lead->lead_remark;
        if(isset($lead->assigns_to->first_name)){
            $assign_to_id = $lead->assigns_to->id;
            $assign_to = $lead->assigns_to->first_name." ".$lead->assigns_to->last_name;
        }else{
            $assign_to_id = "";
            $assign_to = "";
        }
        if(isset($lead->leads_by->first_name)){
            $lead_by_id = $lead->leads_by->id;
            $lead_by = $lead->leads_by->first_name." ".$lead->leads_by->last_name;
        }else{
            $lead_by_id = "";
            $lead_by = "";
        }
        $data["lead_by_id"] = $lead_by_id;
        $data["lead_by"] = $lead_by;
        $data["assign_to_id"] = $assign_to_id;
        $data["assign_to"] = $assign_to;

        $data["profile_id"] = $request->profile_id;
        $data["lead_id"] = $request->lead_id;
        $data["emp_detail"] = !empty($emp_arr)?$emp_arr:NULL;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function veh_update_emp_loan(Request $request)
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

        $admin_by=auth("admin")->user()->id;
        $lead = leads::find($request->lead_id);
        $lead->lead_status = $request->lead_status;
        $lead->updated_by = $admin_by;
        $lead->lead_remark = $request->lead_remark??NULL;
        $lead->update();

        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function destroy($id)
    {
        $leads = leads::find($id);
        $leads->lead_status = 'd';
        $leads->update();
        // $users->delete();
        return redirect()->route('vehicle-loan')->with('success', 'Lead deleted successfully.');
    }

}
