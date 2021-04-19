<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\bank;
use App\Models\city;
use App\Models\fin_product;
use App\Models\pos_income;
use App\Models\pincode;
use App\Models\rel_bank;
use App\Models\rel_fin;
use App\Models\state;
use App\Models\User;
use App\Models\user_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = auth()->user();
        // $users = array();
        // if($user->user_type == "super"){
        //     $users = User::where('id','<>', $user->id)->where("user_status",'>','0')->latest()->paginate(10);
        // }elseif($user->user_type == "coordinator"){
        //     $users = User::where('id','<>', $user->id)
        //             ->where('user_status','>','0')
        //             ->where('user_type','advisor')->latest()->paginate(10);
        // }
        // $users = User::all();
        // return view('crm.userlist',compact('users'));
        return view('crm.userlist');
    }

    public function dashboard(){
        $user = auth()->user();
        if (Auth::check() && (auth()->user()->user_type =="super" || auth()->user()->user_type =="coordinator")) {
            return view('crm.userlist');
        }else{
            return redirect('crms/login');
        }
        // return view('crm.userlist');
    }

    public function otp(Request $request)
    {
        if(isset($request->mobile) && $request->mobile != ""){
            $data = User::where("mobile_no",$request->mobile)->get(["otp"])->first();
            if(isset($data->otp) && $data->otp != "")
                echo $data->otp;
            else
                echo "No Records";
        }else{
            echo "Invalid !!";
        }

    }

    public function getUser(Request $request)
    {
        $sessionArray = Session::get('sessionArray');

        $creditCardModule = [];
        foreach ($sessionArray['permissions'] as $key => $value) {
            if ($value['module']=='creditCard') {
                $creditCardModule = explode(",", $value['permissions']);
            }
        }
        
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
        $records=array();
        // if($user->user_type_id == "1"){
            $totalRecords = User::select('count(*) as allcount')
                            ->where("user_status",'>','0')->count();
            $totalRecordswithFilter = User::select('count(*) as allcount')
                            ->where(function($query) use ($searchValue){
                                $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhere('created_at', 'LIKE', '%'.$searchValue.'%')
                                    // ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
                                    ->orWhereIn('user_type_id',function($query_ut) use ($searchValue){
                                        $query_ut->select('id')->from('user_types')
                                        ->where('title', '%'.$searchValue.'%');
                                     });
                            })
                            ->where("user_status",'>','0')->count();

            // Fetch records
            $records = User::orderBy($columnName,$columnSortOrder)
                ->where(function($query) use ($searchValue){
                    $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
                        ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
                        ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
                        ->orWhere('created_at', 'LIKE', '%'.$searchValue.'%')
                        // ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
                        ->orWhereIn('user_type_id',function($query_ut) use ($searchValue){
                            $query_ut->select('id')->from('user_types')
                            ->where('title', '%'.$searchValue.'%');
                         });
                })
                ->where("user_status",'>','0')
                // ->select('users.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        // }elseif($user->user_type == "coordinator"){
        //     $totalRecords = User::select('count(*) as allcount')->where('id','<>', $user->id)
        //                     ->where('user_status','>','0')
        //                     ->where('user_type','advisor')->count();
        //     $totalRecordswithFilter = User::select('count(*) as allcount')->where('id','<>', $user->id)
        //                     ->where('user_status','>','0')
        //                     ->Where(function ($queri) {
        //                         $queri->where('user_type', 'advisor')
        //                             ->orwhere('user_type', 'user')
        //                             ->orwhere('user_type', 'nh')
        //                             ->orwhere('user_type', 'zh')
        //                             ->orwhere('user_type', 'sh')
        //                             ->orwhere('user_type', 'ch')
        //                             ->orwhere('user_type', 'cth')
        //                             ->orwhere('user_type', 'bdm');
        //                     })
        //                     // ->where('user_type','advisor')
        //                     ->where(function($query) use ($searchValue){
        //                         $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('created_at', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
        //                     })->count();

        //     // Fetch records
        //     $records = User::orderBy($columnName,$columnSortOrder)
        //         ->where(function($query) use ($searchValue){
        //             $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('created_at', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
        //         })
        //         ->where('users.id','<>', $user->id)
        //         ->where('user_status','>','0')
        //         ->Where(function ($queri) {
        //             $queri->where('user_type', 'advisor')
        //                 ->orwhere('user_type', 'user')
        //                 ->orwhere('user_type', 'nh')
        //                 ->orwhere('user_type', 'zh')
        //                 ->orwhere('user_type', 'sh')
        //                 ->orwhere('user_type', 'ch')
        //                 ->orwhere('user_type', 'cth')
        //                 ->orwhere('user_type', 'bdm');
        //         })
        //         // ->where('user_type','advisor')
        //         // ->select('users.*')
        //         ->skip($start)
        //         ->take($rowperpage)
        //         ->get();
        // }
        // die("sssss");
        $data_arr = array();
        if(!empty($records))
        foreach($records as $record){

            if($record->user_status<1){
            $status = "<span class='text-danger'>Deleted</span>";
            }else if ($record->user_status == '1'){
                $status = "<span class='text-warning'>Unverified</span>";
            }else if ($record->user_status == '2'){
            $status = "<span class='text-success'>Verified</span>";
            }

            $name = $record->first_name.' '.$record->last_name;
            if($record->user_code != NULL)
                $user_code = substr($record->user_code,0,6).'/'.substr($record->user_code,6);
            else
                $user_code = $record->user_code;
            $created_at = date("d-m-Y:H:i:s",strtotime($record->created_at));

            $user_type = isset($record->usertype->title)?ucfirst($record->usertype->title):"";

            $pincode = $record->address->pincode->pincode??"";
            $edit = route('crm.edit-user',$record->id);
            $delete = route('crm.delete-user',$record->id);
            
            $action="";
            if (in_array(3, $creditCardModule)) {
                $action = '<a href="'.$edit.'">Edit</a> ';
            }
            if (in_array(4, $creditCardModule)) {
                $action = '  <a href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';
            }
            if ((in_array(3, $creditCardModule)) && (in_array(4, $creditCardModule))) {
                $action = '<a href="'.$edit.'">Edit</a> | <a href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';
            }

            $data_arr[] = array(
                "first_name" => $name,
                "user_code" => $user_code,
                "user_type" => $user_type,
                "pincode" => $pincode,
                "user_status" => $status,
                "created_at" => $created_at,
                "action" => $action,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['banks'] = bank::all(['id','bank_title']);
        $data['states'] = state::where("country_id",'1')->get(["state_name","id"]);
        $data['pos_incomes'] = pos_income::all(['id','title']);
        $data['user_types'] = user_type::where("admin_access","n")->get(['id','title']);
        $data['fin_products'] = fin_product::all(['id','product_name']);
        return view('crm.usercreate', $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    //    echo "<pre>"; print_r($request);die();
        $request->validate([
            'user_type_id'=>['required'],
            'first_name'=>['required'],
            'last_name'=>['required'],
            'email'=>['required', 'unique:users'],
            'mobile_no'=>['required', 'unique:users','min:10','max:10'],
            // 'dob'=>['required'],
            'gender'=>['required'],
            // 'pan_no'=>['required'],
            // 'nominee_name'=>['required'],
            // 'nominee_relation'=>['required'],
            // 'nominee_dob'=>['required'],
            // 'add1'=>['required'],
            'pincode_id'=>['required'],
            'city_id'=>['required'],
            'state_id'=>['required'],
            // 'is_current'=>['required'],
            // 'add_proof_no'=>['required'],
            // 'name_on_bank'=>['required'],
            // 'bank_id'=>['required'],
            // 'account_no'=>['required'],
            // 'ifsc_code'=>['required'],
            // 'uploads'=>['required'],
            // 'qualification'=>['required'],
            // 'pos_income_id'=>['required'],
            // 'total_fn_yr'=>['required'],
            // 'total_fn_month'=>['required'],
            // 'office_space'=>['required'],
            // 'did_sell'=>['required'],
            // 'pos_licence'=>['required'],
            // 'total_bus_anum'=>['required'],
            // 'gst_no'=>['required']
        ]);

        $users = new User();
        $users->first_name = $request->first_name;
        $users->last_name = $request->last_name;
        $users->email = $request->email;
        $users->mobile_no = $request->mobile_no;
        $users->dob = date('Y-m-d',strtotime($request->dob));
        $users->otp = '';
        $users->nominee_name = $request->nominee_name;
        $users->nominee_relation = $request->nominee_relation;
        $users->nominee_dob = date('Y-m-d',strtotime($request->nominee_dob));
        $users->gender = $request->gender;
        $users->qualification = $request->qualification??"hs";
        $users->upload_qual_doc = 'upload_qual_doc';
        $users->pos_income_id = $request->pos_income_id;
        $users->total_fn_yr = $request->total_fn_yr;
        $users->total_fn_month = $request->total_fn_month;
        $users->office_space = $request->office_space??"n";
        // $users->did_sell = $request->did_sell;
        $users->pos_licence = $request->pos_licence??"n";
        $users->total_bus_anum = $request->total_bus_anum;
        $users->pan_no = $request->pan_no;
        $users->upload_pan_no = 'upload_pan_no';
        $users->gst_no = $request->gst_no;
        $users->upload_gst_no = 'upload_gst_no';
        $users->user_type_id = $request->user_type_id;
        $users->are_you = $request->are_you;
        $users->firm_name = $request->firm_name??"";
        $users->password = "aass";
        $users->save();
        $user_id=$users->id;
        $path = 'public/advisors/'.$user_id;
        // if(!Storage::exists($path)){
        //     Storage::makeDirectory($path);
        // }
        $upload_qual_doc = "";
        $upload_pan_no = "";
        $upload_gst_no = "";
        $id_doc_front = "";
        $id_doc_back = "";
        $upload_doc = "";
        if($request->hasFile('upload_qual_doc')){
            // $file=$request->file('upload_qual_doc');
            // $file->getClientOriginalName();
            $upload_qual_doc = substr(str_replace(" ","-",$request->file('upload_qual_doc')->getClientOriginalName()),-10);
            $upload_qual_doc = time().$upload_qual_doc;
            $request->file('upload_qual_doc')->storeAs($path, $upload_qual_doc);
        }
        if($request->hasFile('upload_pan_no')){
            $upload_pan_no = substr(str_replace(" ","-",$request->file('upload_pan_no')->getClientOriginalName()),-10);
            $upload_pan_no = time().$upload_pan_no;
            $request->file('upload_pan_no')->storeAs($path, $upload_pan_no);
        }
        if($request->hasFile('upload_gst_no')){
            $upload_gst_no = substr(str_replace(" ","-",$request->file('upload_gst_no')->getClientOriginalName()),-10);
            $upload_gst_no = time().$upload_gst_no;
            $request->file('upload_gst_no')->storeAs($path, $upload_gst_no);
        }
        if($request->hasFile('id_doc_front')){
            $id_doc_front = substr(str_replace(" ","-",$request->file('id_doc_front')->getClientOriginalName()),-10);
            $id_doc_front = time().$id_doc_front;
            $request->file('id_doc_front')->storeAs($path, $id_doc_front);
        }
        if($request->hasFile('id_doc_back')){
            $id_doc_back = substr(str_replace(" ","-",$request->file('id_doc_back')->getClientOriginalName()),-10);
            $id_doc_back = time().$id_doc_back;
            $request->file('id_doc_back')->storeAs($path, $id_doc_back);
        }
        if($request->hasFile('upload_doc')){
            $upload_doc = substr(str_replace(" ","-",$request->file('upload_doc')->getClientOriginalName()),-10);
            $upload_doc = time().$upload_doc;
            $request->file('upload_doc')->storeAs($path, $upload_doc);
        }

        // $user_type= "BS".strtoupper(substr($request->user_type, 0, 1));
        // if(strlen($user_id)<2){
        //     $user_code = $user_type.'0000'.$user_id;
        // }elseif(strlen($user_id)<3){
        //     $user_code = $user_type.'000'.$user_id;
        // }elseif(strlen($user_id)<4){
        //     $user_code = $user_type.'00'.$user_id;
        // }elseif(strlen($user_id)<5){
        //     $user_code = $user_type.'0'.$user_id;
        // }else{
        //     $user_code = $user_type.$user_id;
        // }
        // SUBSTRING_INDEX(user_code, '/',LENGTH(user_code) - LENGTH(REPLACE(user_code, '/', '')))

        $pincode = pincode::find($request->pincode_id);
        $u_code = DB::select("select SUBSTRING(user_code,7,LENGTH(user_code)) as code from users where LEFT(user_code, 6) = ? and user_type = 'advisor'", [$pincode->pincode]);
        $codeArr=array();
        if(!empty($u_code))
        foreach($u_code as $val){
            $codeArr[] = $val->code;
        }

        if(!empty($codeArr)){
            $new_count=max($codeArr)+1;
            $user_code = ($new_count > 9)? $pincode->pincode . $new_count : $pincode->pincode . "0" . $new_count;
        }else $user_code = $pincode->pincode."01";
        $pass = Hash::make($user_code);


        User::where('id',$user_id)->update(['upload_qual_doc'=>$upload_qual_doc,'upload_pan_no'=>$upload_pan_no,'upload_gst_no'=>$upload_gst_no,'user_code'=>$user_code,'password'=>$pass]);

        // $pincode_id = DB::table('pincodes')->where('pincode', $request->pincode_id)->first();
        // print_r($pincode_id);die;
        $addresses = new Address();
        $addresses->user_id = $user_id;
        $addresses->add1 = $request->add1;
        $addresses->pincode_id = $request->pincode_id;
        $addresses->city_id = $request->city_id;
        $addresses->state_id = $request->state_id;
        $addresses->is_current = $request->is_current??"y";
        $addresses->add_proof = $request->add_proof??"d";
        $addresses->add_proof_no = $request->add_proof_no;
        $addresses->id_doc_front = $id_doc_front;
        $addresses->id_doc_back = $id_doc_back;
        $addresses->save();

        if($request->bank_id != ""){
            $relBanks = new rel_bank();
            $relBanks->bank_id = $request->bank_id;
            $relBanks->user_id = $user_id;
            $relBanks->name_on_bank = $request->name_on_bank;
            $relBanks->account_no = $request->account_no;
            $relBanks->ifsc_code = $request->ifsc_code;
            $relBanks->uploads = $request->uploads??'cheque';
            $relBanks->upload_doc = $upload_doc;
            $relBanks->save();
        }


        $relFin = [];
        if(!empty($request->did_sell))
        foreach($request->did_sell as $value){
            if($value != "")
            $relFin[] = [
                'user_id' => $user_id,
                'fin_product_id' => $value
            ];
        }
        if(!empty($relFin)) rel_fin::insert($relFin);

        return redirect()->route('crm.add-user')->with('success', 'Project created successfully.');
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
        $data['banks'] = bank::all(['id','bank_title']);
        $data['states'] = state::where("country_id",'1')->get(["state_name","id"]);
        $data['pos_incomes'] = pos_income::all(['id','title']);
        $data['fin_products'] = fin_product::all(['id','product_name']);
        $data['user_types'] = user_type::where("admin_access","n")->get(['id','title']);
        $data["users"] = $user = User::find($id);
        // echo $user->relbank->bank_id."sss"; die;
        $data["cities"] = city::where("state_id",$user->address->state_id??"")->get(['id','city_name']);
        $data["rel_fins"] = rel_fin::where("user_id", $id)->get(["fin_product_id"]);
        // echo url("/posts/{$id}");die;
        // $aa = address::where('pincode_id',$user->address->pincode_id)->count();
        // $pincode = pincode::find($user->address->pincode_id);

        // echo "<pre>";print_r($codeArr);die;
        return view('crm.useredit', $data);
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
        $request->validate([
            'user_type_id'=>['required'],
            'first_name'=>['required'],
            'last_name'=>['required'],
            'email'=>['required', "unique:users,email,$id"],
            'mobile_no'=>['required', "unique:users,mobile_no,$id",'min:10','max:10'],
            // 'dob'=>['required'],
            'gender'=>['required'],
            // 'pan_no'=>['required'],
            // 'nominee_name'=>['required'],
            // 'nominee_relation'=>['required'],
            // 'nominee_dob'=>['required'],
            // 'add1'=>['required'],
            'pincode_id'=>['required'],
            'city_id'=>['required'],
            'state_id'=>['required'],
            // 'is_current'=>['required'],
            // 'add_proof_no'=>['required'],
            // 'name_on_bank'=>['required'],
            // 'bank_id'=>['required'],
            // 'account_no'=>['required'],
            // 'ifsc_code'=>['required'],
            // 'uploads'=>['required'],
            // 'qualification'=>['required'],
            // 'pos_income_id'=>['required'],
            // 'total_fn_yr'=>['required'],
            // 'total_fn_month'=>['required'],
            // 'office_space'=>['required'],
            // 'did_sell'=>['required'],
            // 'pos_licence'=>['required'],
            // 'total_bus_anum'=>['required'],
            // 'gst_no'=>['required']
        ]);
        $user_id=$request->user_id;
        $path = 'public/advisors/'.$user_id;
        // if(!Storage::exists($path)){
        //     Storage::makeDirectory($path);
        // }
        $upload_qual_doc = "";
        $upload_pan_no = "";
        $upload_gst_no = "";
        $id_doc_front = "";
        $id_doc_back = "";
        $upload_doc = "";
        if($request->hasFile('upload_qual_doc')){
            // $file=$request->file('upload_qual_doc');
            // $file->getClientOriginalName();
            $upload_qual_doc = substr(str_replace(" ","-",$request->file('upload_qual_doc')->getClientOriginalName()),-10);
            $upload_qual_doc = time().$upload_qual_doc;
            $request->file('upload_qual_doc')->storeAs($path, $upload_qual_doc);
        }
        if($request->hasFile('upload_pan_no')){
            $upload_pan_no = substr(str_replace(" ","-",$request->file('upload_pan_no')->getClientOriginalName()),-10);
            $upload_pan_no = time().$upload_pan_no;
            $request->file('upload_pan_no')->storeAs($path, $upload_pan_no);
        }
        if($request->hasFile('upload_gst_no')){
            $upload_gst_no = substr(str_replace(" ","-",$request->file('upload_gst_no')->getClientOriginalName()),-10);
            $upload_gst_no = time().$upload_gst_no;
            $request->file('upload_gst_no')->storeAs($path, $upload_gst_no);
        }
        if($request->hasFile('id_doc_front')){
            $id_doc_front = substr(str_replace(" ","-",$request->file('id_doc_front')->getClientOriginalName()),-10);
            $id_doc_front = time().$id_doc_front;
            $request->file('id_doc_front')->storeAs($path, $id_doc_front);
        }
        if($request->hasFile('id_doc_back')){
            $id_doc_back = substr(str_replace(" ","-",$request->file('id_doc_back')->getClientOriginalName()),-10);
            $id_doc_back = time().$id_doc_back;
            $request->file('id_doc_back')->storeAs($path, $id_doc_back);
        }
        if($request->hasFile('upload_doc')){
            $upload_doc = substr(str_replace(" ","-",$request->file('upload_doc')->getClientOriginalName()),-10);
            $upload_doc = time().$upload_doc;
            $request->file('upload_doc')->storeAs($path, $upload_doc);
        }
// die($upload_doc);
        $users = User::find($user_id);
        $users->first_name = $request->first_name;
        $users->last_name = $request->last_name;
        $users->email = $request->email;
        $users->mobile_no = $request->mobile_no;
        $users->dob = date('Y-m-d',strtotime($request->dob));
        $users->nominee_name = $request->nominee_name;
        $users->nominee_relation = $request->nominee_relation;
        $users->nominee_dob = date('Y-m-d',strtotime($request->nominee_dob));
        $users->gender = $request->gender;
        $users->qualification = $request->qualification;
        if($upload_qual_doc != "") $users->upload_qual_doc = $upload_qual_doc;
        $users->pos_income_id = $request->pos_income_id;
        $users->total_fn_yr = $request->total_fn_yr;
        $users->total_fn_month = $request->total_fn_month;
        $users->office_space = $request->office_space;
        // $users->did_sell = $request->did_sell;
        $users->pos_licence = $request->pos_licence;
        $users->total_bus_anum = $request->total_bus_anum;
        $users->pan_no = $request->pan_no;
        if($upload_pan_no != "") $users->upload_pan_no = $upload_pan_no;
        $users->gst_no = $request->gst_no;
        if($upload_gst_no != "") $users->upload_gst_no = $upload_gst_no;
        $users->user_type_id = $request->user_type_id;
        $users->are_you = $request->are_you;
        $users->firm_name = $request->firm_name;
        $users->user_status = $request->user_status;
        $users->update();

        // $pincode_id = DB::table('pincodes')->where('pincode', $request->pincode_id)->first();
        $pincode_id = $request->pincode_id;
        // print_r($pincode_id);die;
        if(isset($users->address->id)){
            $addresses = Address::find($users->address->id);
            $addresses->user_id = $user_id;
            $addresses->add1 = $request->add1;
            $addresses->pincode_id = $pincode_id;
            $addresses->city_id = $request->city_id;
            $addresses->state_id = $request->state_id;
            $addresses->is_current = $request->is_current;
            $addresses->add_proof = $request->add_proof;
            $addresses->add_proof_no = $request->add_proof_no;
            if($id_doc_front != "") $addresses->id_doc_front = $id_doc_front;
            if($id_doc_back != "") $addresses->id_doc_back = $id_doc_back;
            $addresses->update();
        }else{
            $addresses = new Address();
            $addresses->user_id = $user_id;
            $addresses->add1 = $request->add1;
            $addresses->pincode_id = $request->pincode_id;
            $addresses->city_id = $request->city_id;
            $addresses->state_id = $request->state_id;
            $addresses->is_current = $request->is_current??"y";
            $addresses->add_proof = $request->add_proof??"d";
            $addresses->add_proof_no = $request->add_proof_no;
            $addresses->id_doc_front = $id_doc_front;
            $addresses->id_doc_back = $id_doc_back;
            $addresses->save();
        }

        if($request->bank_id != ""){
            if(isset($users->relbank->id)){
                $relBanks = rel_bank::find($users->relbank->id);
                $relBanks->bank_id = $request->bank_id;
                $relBanks->user_id = $user_id;
                $relBanks->name_on_bank = $request->name_on_bank;
                $relBanks->account_no = $request->account_no;
                $relBanks->ifsc_code = $request->ifsc_code;
                $relBanks->uploads = $request->uploads;
                if($upload_doc != "") $relBanks->upload_doc = $upload_doc;
                $relBanks->update();
            }else{
                $relBanks = new rel_bank();
                $relBanks->bank_id = $request->bank_id;
                $relBanks->user_id = $user_id;
                $relBanks->name_on_bank = $request->name_on_bank;
                $relBanks->account_no = $request->account_no;
                $relBanks->ifsc_code = $request->ifsc_code;
                $relBanks->uploads = $request->uploads??'cheque';
                $relBanks->upload_doc = $upload_doc;
                $relBanks->save();
            }
        }
        // $2y$10$GgNELAgC9tE.Mw/u70XULu1knQVFbw7.nwDjbqU8TpmB14op0wZJS
        rel_fin::where('user_id', $user_id)->delete();
        $relFin = [];
        if(!empty($request->did_sell))
        foreach($request->did_sell as $value){
            if($value != "")
            $relFin[] = [
                'user_id' => $user_id,
                'fin_product_id' => $value
            ];
        }
        if(!empty($relFin)) rel_fin::insert($relFin);

        return redirect()->route('crm.users')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = User::find($id);
        $users->user_status = '0';
        $users->update();
        // $users->delete();
        return redirect()->route('crm.users')->with('success', 'User deleted successfully.');
    }

    public function change_pass(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        return redirect()->route('crm.users')->with('success', 'Password successfully.');
    }
    public function change_password (Request $request)
    {
        return view('crm.change_pass');
    }
    public function deleted()
    {
        // $user = auth()->user();
        // $users = User::where('id','<>', $user->id)->where("user_status",'0')->latest()->paginate(5);
        // $users = User::all();
        return view('crm.deleted_user');
    }
    public function getDeleted(Request $request)
    {
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
        // if($user->user_type == "super"){
            $totalRecords = User::select('count(*) as allcount')
                            ->where("user_status",'0')->count();
            $totalRecordswithFilter = User::select('count(*) as allcount')
                            ->where(function($query) use ($searchValue){
                                $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
                                    // ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
                                    ->orWhereIn('user_type_id',function($query_ut) use ($searchValue){
                                        $query_ut->select('id')->from('user_types')
                                        ->where('title', '%'.$searchValue.'%');
                                     });
                            })
                            ->where("user_status",'0')->count();

            // Fetch records
            $records = User::orderBy($columnName,$columnSortOrder)
                ->where(function($query) use ($searchValue){
                    $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
                        ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
                        ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
                        // ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
                        ->orWhereIn('user_type_id',function($query_ut) use ($searchValue){
                            $query_ut->select('id')->from('user_types')
                            ->where('title', '%'.$searchValue.'%');
                         });
                })
                ->where("user_status",'0')
                // ->select('users.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        // }elseif($user->user_type == "coordinator"){
        //     $totalRecords = User::select('count(*) as allcount')->where('id','<>', $user->id)
        //                     ->where('user_status','0')
        //                     ->where('users.id','<>', $user->id)
        //                     ->where('user_type','advisor')->count();
        //     $totalRecordswithFilter = User::select('count(*) as allcount')->where('id','<>', $user->id)
        //                     ->where('user_status','0')
        //                     ->where('users.id','<>', $user->id)
        //                     ->Where(function ($queri) {
        //                         $queri->where('user_type', 'advisor')
        //                             ->orwhere('user_type', 'user')
        //                             ->orwhere('user_type', 'nh')
        //                             ->orwhere('user_type', 'zh')
        //                             ->orwhere('user_type', 'sh')
        //                             ->orwhere('user_type', 'ch')
        //                             ->orwhere('user_type', 'cth')
        //                             ->orwhere('user_type', 'bdm');
        //                     })
        //                     // ->where('user_type','advisor')
        //                     ->where(function($query) use ($searchValue){
        //                         $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
        //                             ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
        //                     })->count();

        //     // Fetch records
        //     $records = User::orderBy($columnName,$columnSortOrder)
        //         ->where(function($query) use ($searchValue){
        //             $query->where('first_name', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('user_code', 'LIKE', '%'.$searchValue.'%')
        //                 ->orWhere('user_type', 'LIKE', '%'.$searchValue.'%');
        //         })
        //         ->where('users.id','<>', $user->id)
        //         ->where('user_status','0')
        //         ->Where(function ($queri) {
        //             $queri->where('user_type', 'advisor')
        //                 ->orwhere('user_type', 'user')
        //                 ->orwhere('user_type', 'nh')
        //                 ->orwhere('user_type', 'zh')
        //                 ->orwhere('user_type', 'sh')
        //                 ->orwhere('user_type', 'ch')
        //                 ->orwhere('user_type', 'cth')
        //                 ->orwhere('user_type', 'bdm');
        //         })
        //         // ->where('user_type','advisor')
        //         // ->select('users.*')
        //         ->skip($start)
        //         ->take($rowperpage)
        //         ->get();
        // }
        $data_arr = array();
        if(!empty($records))
        foreach($records as $record){

            if($record->user_status<1){
            $status = "<span class='text-danger'>Deleted</span>";
            }else if ($record->user_status == '1'){
                $status = "<span class='text-warning'>Unverified</span>";
            }else if ($record->user_status == '2'){
            $status = "<span class='text-success'>Verified</span>";
            }

            $name = $record->first_name.' '.$record->last_name;
            if($record->user_code != NULL)
                $user_code = substr($record->user_code,0,6).'/'.substr($record->user_code,6);
            else
                $user_code = $record->user_code;

            $user_type = isset($record->usertype->title)?ucfirst($record->usertype->title):"";
            $pincode = $record->address->pincode->pincode??"";
            $edit = route('crm.edit-user',$record->id);
            $delete = route('crm.delete-user',$record->id);
            $action = '<a href="'.$edit.'">Edit</a> | <a href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';

            $data_arr[] = array(
                "first_name" => $name,
                "user_code" => $user_code,
                "user_type" => $user_type,
                "pincode" => $pincode,
                "user_status" => $status,
                "action" => $action,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }
}
