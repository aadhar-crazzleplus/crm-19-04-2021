<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjectResource;
use App\Models\companies;
use App\Models\lead_addresses;
use App\Models\lead_is_loans;
use App\Models\lead_profiles;
use App\Models\leads;
use App\Models\Wallet;
use App\Models\products;
use App\Models\state;
use App\Models\temp_otp;

class PersonalLoanController extends BaseController
{
    public function pl_view(){
        $data['states'] = state::where("country_id",'1')->get(["state_name","id"]);
        return view("crm.pl_view",$data);
    }


    public function getplleads(Request $request){
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
        $product_id = 4;
        // $leads = leads::where("product_id","9")->get();
        $totalRecords = leads::select('count(*) as allcount')
                            ->where("product_id",$product_id)->count();

       $totalRecordswithFilter = leads::select('count(*) as allcount')
                            ->where(function($query) use ($searchValue){
                                $query->where('lead_status', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhereIn('assign_to',function($u_query) use ($searchValue){
                                        $u_query->select('id')->from('users')
                                        ->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%');
                                     })
                                    ->orWhereIn('lead_profile_id',function($pro_query) use ($searchValue){
                                        $pro_query->select('id')->from('lead_profiles')
                                        ->where('full_name', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('monthly_salary', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('company_vintage', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('mobile_no', 'LIKE', '%'.$searchValue.'%');
                                    });
                            })
                            ->where("lead_status","<>","d")
                            ->where("product_id",$product_id)->count();
        // Fetch records
        $leads = leads::orderBy($columnName,$columnSortOrder)
                    ->where(function($query) use ($searchValue){
                        $query->where('lead_status', 'LIKE', '%'.$searchValue.'%')
                            ->orWhereIn('assign_to',function($u_query) use ($searchValue){
                                $u_query->select('id')->from('users')
                                ->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%');
                            })
                            ->orWhereIn('lead_profile_id',function($pro_query) use ($searchValue){
                                $pro_query->select('id')->from('lead_profiles')
                                ->where('full_name', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('monthly_salary', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('company_vintage', 'LIKE', '%'.$searchValue.'%')
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

            $edit = route('pl-basic-edit',$lead->id);
            $delete = route('pl-del',$lead->id);
            $action = '<a data-toggle="modal" data-target="#editModal2" href="#editModal2" onclick="editLead(\''.$edit.'\');">Edit</a> | <a href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';
            if(isset($lead->assigns_to->first_name))
                $assign_to = $lead->assigns_to->first_name." ".$lead->assigns_to->last_name;
            else $assign_to = "";
            $lead_arr[]=[
                // "lead_id"=>$lead->id,
                "full_name"=>'<a data-toggle="modal" data-target="#showModal3" href="#showModal3" onclick="showLead(\''.$lead->id.'\');">'.$lead->lead_profile->full_name.'</a>',
                "mobile_no"=>$lead->lead_profile->mobile_no,
                "monthly_salary"=>$lead->lead_profile->monthly_salary,
                "company_vintage"=>$lead->lead_profile->company_vintage,
                "assign_to"=>$assign_to,
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

    public function pl_basic_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $lead = lead_profiles::where("mobile_no",$request->mobile_no)->first();
        $personal=array();
        if(!empty($lead)){
            $personal["profile_id"]=$lead->id;
            $personal["full_name"]=$lead->full_name;
            $personal["mobile_no"]=$lead->mobile_no;
            $personal["email"]=$lead->email;
            $personal["dob"]=$lead->dob;
        }else{
            $personal["profile_id"]="";
            $personal["full_name"]="";
            $personal["mobile_no"]=$request->mobile_no;
            $personal["email"]="";
            $personal["dob"]="";
        }
        $add=array();
        if(isset($lead->lead_address->id)){
            $add['address_id'] = $lead->lead_address->id;
            $add['address'] = $lead->lead_address->address;
            $add['pincode_id'] = $lead->lead_address->pincode_id;
            $add['city_id'] = $lead->lead_address->city_id;
            $add['state_id'] = $lead->lead_address->state_id;
        }else{
            $add['address_id'] = "";
            $add['address'] = "";
            $add['pincode_id'] = "";
            $add['city_id'] = "";
            $add['state_id'] = "";
        }

        $data["profile"] = $personal;
        $data["address"] = $add;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");

    }

    public function pl_profile_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'mobile_no' => 'required',
            'address' => 'required',
            'dob' => 'required',
            'pincode_id' => "required",
            'lead_by' => "required",
            'assign_to' => "required",
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if(isset($request->profile_id) && $request->profile_id != "" && $request->profile_id > 0){
            $lead_pro = lead_profiles::find($request->profile_id);
            if(!empty($lead_pro) && $lead_pro->mobile_no != ""){
                $lead_pro->full_name = $request->full_name;
                $lead_pro->mobile_no = $request->mobile_no;
                $lead_pro->email = $request->email;
                $lead_pro->dob = date('Y-m-d',strtotime($request->dob));
                $lead_pro->update();
            }else{
                $lead_pro->full_name = $request->full_name;
                $lead_pro->mobile_no = $request->mobile_no;
                $lead_pro->email = $request->email;
                $lead_pro->dob = date('Y-m-d',strtotime($request->dob));
                $lead_pro->save();
            }
        }else{
            $lead_pro = new lead_profiles();
            $lead_pro->full_name = $request->full_name;
            $lead_pro->mobile_no = $request->mobile_no;
            $lead_pro->email = $request->email;
            $lead_pro->dob = date('Y-m-d',strtotime($request->dob));
            $lead_pro->save();
        }
        $product_id = 4;
        $admin_by=auth("admin")->user()->id;
        if(isset($request->address_id) && $request->address_id > 0){
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

            $lead = leads::where('lead_by', $request->lead_by)
                    ->where('product_id',$product_id)
                    ->where('lead_profile_id',$lead_pro->id)->first();
            if(empty($lead)){
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
            }else{
                $lead->lead_by = $request->lead_by;
                $lead->assign_to = $request->assign_to;
                $lead->close_by = $request->assign_to;
                $lead->updated_by = $admin_by;
                $lead->product_id = $product_id;
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
        }

        // $is_loan = lead_is_loans::where("lead_profile_id",$lead_pro->id)->first();
        // $loan_arr=array();
        // if(!empty($is_loan)){
        //     $loan_arr["loan_id"]=$is_loan->id;
        //     $loan_arr["total_rem_loan"]=$is_loan->total_rem_loan;
        //     $loan_arr["monthly_emi"]=$is_loan->monthly_emi;
        // }else{
        //     $loan_arr["loan_id"]="";
        //     $loan_arr["total_rem_loan"]="";
        //     $loan_arr["monthly_emi"]="";
        // }

        $data["profile_id"] = $lead_pro->id;
        $data["lead_id"] = $lead->id;
        // $data["is_loan"] = !empty($loan_arr)?$loan_arr:NULL;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function pl_is_loan_add(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'total_rem_loan' => 'required',
        //     'monthly_emi' => 'required',
        //     'profile_id' => 'required',
        //     'lead_id' => 'required'
        // ]);

        // if($validator->fails()){
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }

        if($request->total_rem_loan >0 || $request->monthly_emi > 0){
            if(isset($request->loan_id) && $request->loan_id > 0){
                $is_loan = lead_is_loans::find($request->loan_id);
                if(!empty($is_loan) && $is_loan->total_rem_loan != ""){
                    $is_loan->total_rem_loan = $request->total_rem_loan;
                    $is_loan->monthly_emi = $request->monthly_emi;
                    $is_loan->update();
                }else{
                    return $this->sendError('Loan Validation.', array("err_msg"=>["Loan Id does not exist !!"]));
                }

            }else{
                $is_loan = new lead_is_loans();
                $is_loan->lead_profile_id = $request->profile_id;
                $is_loan->total_rem_loan = $request->total_rem_loan;
                $is_loan->monthly_emi = $request->monthly_emi;
                $is_loan->save();
            }
        }

        $res_arr = lead_profiles::where("id",$request->profile_id)->first();
        $arr_inocome=array();
        if(!empty($res_arr) && $res_arr->monthly_salary != ""){
            $arr_inocome["monthly_salary"]=$res_arr->monthly_salary;
            $arr_inocome["company_id"]=$res_arr->company_id;
            $arr_inocome["company_name"]=$res_arr->company->company_name??"";
            $arr_inocome["designation"]=$res_arr->designation;
            $arr_inocome["company_vintage"]=$res_arr->company_vintage??"";
            $arr_inocome["office_email"]=$res_arr->office_email;
            $arr_inocome["company_address"]=$res_arr->company->address??"";
            $arr_inocome["pincode_id"]=$res_arr->company->pincode_id??"";
            $arr_inocome["city_id"]=$res_arr->company->city_id??"";
            $arr_inocome["state_id"]=$res_arr->company->state_id??"";
        }else{
            $arr_inocome["monthly_salary"]="";
            $arr_inocome["company_id"]="";
            $arr_inocome["company_name"]="";
            $arr_inocome["designation"]="";
            $arr_inocome["company_vintage"]="";
            $arr_inocome["office_email"]="";
            $arr_inocome["company_address"]="";
            $arr_inocome["pincode_id"]="";
            $arr_inocome["city_id"]="";
            $arr_inocome["state_id"]="";
        }
        $data["profile_id"] = $request->profile_id;
        $data["lead_id"] = $request->lead_id;
        $data["income"] = !empty($arr_inocome)?$arr_inocome:NULL;
        
        $userId = auth("admin")->user()->id;
        $user_type_id = auth("admin")->user()->user_type_id;

        $wallet = new Wallet;
        $wallet->lead_id = $request->lead_id;
        $wallet->user_id = $userId;
        $wallet->save();
        $str = "WLLT";
        $walletId = str_pad($str,8,0);
        $wallet->unique_id = $walletId.$wallet->id;
        $wallet->save();

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function pl_income_add(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'monthly_salary' => 'required',
            'company_id' => 'required',
            // 'company_name' => 'required',
            'designation' => 'required',
            'company_vintage' => 'required',
            'office_email' => 'required',
            'company_address' => 'required',
            'pincode_id' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'profile_id' => 'required',
            'lead_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $res_arr = lead_profiles::where("id",$request->profile_id)->first();
        $com_id = (int)$request->company_id;
        if(is_int($com_id) && $com_id > 0){
            $company_id = $request->company_id;
        }else{
            $company_name = $request->company_id;
            $cm_name = ucwords(trim(preg_replace('/\s+/', ' ', $company_name)));
            $cm_arr = companies::where('company_name',$cm_name)->first();
            if(!$cm_arr){
                $add_cm["company_name"] = $cm_name;
                $add_cm['address']=$request->company_address;
                $add_cm['pincode_id']=$request->pincode_id;
                $add_cm['city_id']=$request->city_id;
                $add_cm['state_id']=$request->state_id;
                $add_cm['status'] ='1';
                $cm_create = companies::create($add_cm);
                $company_id = $cm_create->id;
            }else{
                $company_id = $cm_arr->id;
            }
        }
        $res_arr->occupation_id="1";
        $res_arr->monthly_salary=$request->monthly_salary;
        $res_arr->company_id=$company_id;
        $res_arr->designation=$request->designation;
        $res_arr->company_vintage=$request->company_vintage;
        $res_arr->office_email=$request->office_email;
        $res_arr->update();
        $lead = leads::find($request->lead_id);
        $lead->lead_status = 'p';
        $lead->update();

        $data["lead_id"] = $request->lead_id;
        $data["profile_id"] = $request->profile_id;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    // edit
    public function pl_basic_edit($id)
    {
        $lead = leads::find($id);
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

        $personal=array();
        if(!empty($lead->lead_profile)){
            $personal["profile_id"]=$lead->lead_profile->id;
            $personal["full_name"]=$lead->lead_profile->full_name;
            $personal["mobile_no"]=$lead->lead_profile->mobile_no;
            $personal["email"]=$lead->lead_profile->email;
            $personal["dob"]=date('d-m-Y',strtotime($lead->lead_profile->dob));
        }else{
            $personal["profile_id"]="";
            $personal["full_name"]="";
            $personal["mobile_no"]="";
            $personal["email"]="";
            $personal["dob"]="";
        }
        $add=array();
        if(isset($lead->lead_address->id)){
            $add['address_id'] = $lead->lead_address->id;
            $add['address'] = $lead->lead_address->address;
            $add['pincode_id'] = $lead->lead_address->pincode_id;
            $add['city_id'] = $lead->lead_address->city_id;
            $add['state_id'] = $lead->lead_address->state_id;
        }else{
            $add['address_id'] = "";
            $add['address'] = "";
            $add['pincode_id'] = "";
            $add['city_id'] = "";
            $add['state_id'] = "";
        }

        $data["profile"] = $personal;
        $data["address"] = $add;
        $data["lead_by_id"] = $lead_by_id;
        $data["lead_by"] = $lead_by;
        $data["assign_to_id"] = $assign_to_id;
        $data["assign_to"] = $assign_to;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");

    }

    public function pl_profile_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'mobile_no' => 'required',
            'address' => 'required',
            'dob' => 'required',
            'pincode_id' => "required",
            'address_id' => "required",
            'lead_by' => "required",
            'assign_to' => "required",
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $lead_pro = lead_profiles::find($request->profile_id);
        if(!empty($lead_pro) && $lead_pro->mobile_no != ""){
            $lead_pro->full_name = $request->full_name;
            $lead_pro->mobile_no = $request->mobile_no;
            $lead_pro->email = $request->email;
            $lead_pro->dob = date('Y-m-d',strtotime($request->dob));
            $lead_pro->update();
        }else{
            return $this->sendError('Profile Validation.', array("err_msg"=>["Profile Id does not exist !!"]));
        }
        $product_id = 4;
        $admin_by=auth("admin")->user()->id;
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

        $lead = leads::where('lead_by', $request->lead_by)
                ->where('product_id',$product_id)
                ->where('lead_profile_id',$lead_pro->id)->first();
        if(!empty($lead)){
            $lead->lead_by = $request->lead_by;
            $lead->assign_to = $request->assign_to;
            $lead->close_by = $request->assign_to;
            $lead->admin_by = $admin_by;
            $lead->product_id = $product_id;
            $lead->lead_profile_id = $lead_pro->id;
            // $lead->lead_status = 'i';
            $lead->update();
        }else{
            return $this->sendError('Lead Validation.', array("err_msg"=>["Lead does not exist !!"]));
        }

        $is_loan = lead_is_loans::where("lead_profile_id",$lead_pro->id)->first();
        $loan_arr=array();
        if(!empty($is_loan)){
            $loan_arr["loan_id"]=$is_loan->id;
            $loan_arr["total_rem_loan"]=$is_loan->total_rem_loan;
            $loan_arr["monthly_emi"]=$is_loan->monthly_emi;
        }else{
            $loan_arr["loan_id"]="";
            $loan_arr["total_rem_loan"]="";
            $loan_arr["monthly_emi"]="";
        }

        $data["profile_id"] = $lead_pro->id;
        $data["lead_id"] = $lead->id;
        $data["lead_status"] = $lead->lead_status;
        $data["lead_remark"] = $lead->lead_remark;
        $data["is_loan"] = !empty($loan_arr)?$loan_arr:NULL;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function pl_is_loan_update(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'total_rem_loan' => 'required',
        //     'monthly_emi' => 'required',
        //     'profile_id' => 'required',
        //     'lead_id' => 'required'
        // ]);

        // if($validator->fails()){
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }

        if($request->total_rem_loan >0 || $request->monthly_emi > 0){
            if(isset($request->loan_id) && $request->loan_id > 0){
                $is_loan = lead_is_loans::find($request->loan_id);
                if(!empty($is_loan) && $is_loan->total_rem_loan != ""){
                    $is_loan->total_rem_loan = $request->total_rem_loan;
                    $is_loan->monthly_emi = $request->monthly_emi;
                    $is_loan->update();
                }else{
                    return $this->sendError('Loan Validation.', array("err_msg"=>["Loan Id does not exist !!"]));
                }

            }else{
                $is_loan = new lead_is_loans();
                $is_loan->lead_profile_id = $request->profile_id;
                $is_loan->total_rem_loan = $request->total_rem_loan;
                $is_loan->monthly_emi = $request->monthly_emi;
                $is_loan->save();
            }
        }

        $res_arr = lead_profiles::where("id",$request->profile_id)->first();
        $arr_inocome=array();
        if(!empty($res_arr) && $res_arr->monthly_salary != ""){
            $arr_inocome["monthly_salary"]=$res_arr->monthly_salary;
            $arr_inocome["company_id"]=$res_arr->company_id;
            $arr_inocome["company_name"]=$res_arr->company->company_name;
            $arr_inocome["designation"]=$res_arr->designation;
            $arr_inocome["company_vintage"]=$res_arr->company_vintage;
            $arr_inocome["office_email"]=$res_arr->office_email;
            $arr_inocome["company_address"]=$res_arr->company->address;
            $arr_inocome["pincode_id"]=$res_arr->company->pincode_id;
            $arr_inocome["city_id"]=$res_arr->company->city_id;
            $arr_inocome["state_id"]=$res_arr->company->state_id;
        }else{
            $arr_inocome["monthly_salary"]="";
            $arr_inocome["company_id"]="";
            $arr_inocome["company_name"]="";
            $arr_inocome["designation"]="";
            $arr_inocome["company_vintage"]="";
            $arr_inocome["office_email"]="";
            $arr_inocome["company_address"]="";
            $arr_inocome["pincode_id"]="";
            $arr_inocome["city_id"]="";
            $arr_inocome["state_id"]="";
        }
        $data["profile_id"] = $request->profile_id;
        $data["lead_id"] = $request->lead_id;
        $data["lead_remark"] = $request->lead_remark;
        $data["lead_status"] = $request->lead_status;
        $data["income"] = !empty($arr_inocome)?$arr_inocome:NULL;

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function pl_income_update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'monthly_salary' => 'required',
            'company_id' => 'required',
            // 'company_name' => 'required',
            'designation' => 'required',
            'company_vintage' => 'required',
            'office_email' => 'required',
            'company_address' => 'required',
            'pincode_id' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'profile_id' => 'required',
            'lead_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $res_arr = lead_profiles::where("id",$request->profile_id)->first();
        $com_id = (int)$request->company_id;
        if(is_int($com_id) && $com_id > 0){
            $company_id = $request->company_id;
        }else{
            $company_name = $request->company_id;
            $cm_name = ucwords(trim(preg_replace('/\s+/', ' ', $company_name)));
            $cm_arr = companies::where('company_name',$cm_name)->first();
            if(!$cm_arr){
                $add_cm["company_name"] = $cm_name;
                $add_cm['address']=$request->company_address;
                $add_cm['pincode_id']=$request->pincode_id;
                $add_cm['city_id']=$request->city_id;
                $add_cm['state_id']=$request->state_id;
                $add_cm['status'] ='1';
                $cm_create = companies::create($add_cm);
                $company_id = $cm_create->id;
            }else{
                $company_id = $cm_arr->id;
            }
        }
        $res_arr->occupation_id="1";
        $res_arr->monthly_salary=$request->monthly_salary;
        $res_arr->company_id=$company_id;
        $res_arr->designation=$request->designation;
        $res_arr->company_vintage=$request->company_vintage;
        $res_arr->office_email=$request->office_email;
        $res_arr->update();

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


    public function pl_show(Request $request){
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


        $is_loan = lead_is_loans::where("lead_profile_id",$lead->lead_profile_id)->first();
        $loan_arr=array();
        if(!empty($is_loan)){
            $data["total_rem_loan"]=$is_loan->total_rem_loan;
            $data["monthly_emi"]=$is_loan->monthly_emi;
        }else{
            $data["total_rem_loan"]="N/A";
            $data["monthly_emi"]="N/A";
        }

        $data["monthly_salary"]=$lead->lead_profile->monthly_salary;
        $data["company_name"]=$lead->lead_profile->company->company_name;
        $data["designation"]=$lead->lead_profile->designation;
        $data["company_vintage"]=$lead->lead_profile->company_vintage;
        $data["office_email"]=$lead->lead_profile->office_email;
        $data["company_address"]=$lead->lead_profile->company->address.", ".$lead->lead_profile->company->city->city_name
        .", ".$lead->lead_profile->company->state->state_name." - ".$lead->lead_profile->company->pincode->pincode;

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

    public function pl_deleted(Request $request)
    {

    }
    public function pl_del($id)
    {
        $leads = leads::find($id);
        $leads->lead_status = 'd';
        $leads->update();
        // $users->delete();
        return redirect()->route('pl-view')->with('success', 'Lead deleted successfully.');
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
}
