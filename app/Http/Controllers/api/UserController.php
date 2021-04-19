<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Address;
use App\Models\app_details;
use App\Models\bank;
use App\Models\city;
use App\Models\fin_product;
use App\Models\pincode;
use App\Models\pos_income;
use App\Models\rel_bank;
use App\Models\rel_fin;
use App\Models\state;
use App\Models\User;
use App\Models\Wallet;
use App\Models\user_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function checkupdate(Request $request)
    {
        $vesion = app_details::first();
        if(!$vesion){
            return $this->sendError('Version Error.', array("err_msg"=>["Version not found !!"]));
        }
        // `user_id`, `admin_id`, `ip`, `mobile_no`, `activity_at`,
        // `lags_longs`, `device_id`, `app_version`, `mobile_type`
        $currentURL = $request->url();
        $currentURL = ($currentURL != "")?$currentURL:$request->path();
        $local_ip = isset($request->ip)?$request->ip:"";
        $ip = $request->ip();
        $data["user_id"] = $request->user_id??"";
        $data["ip"] = $ip;
        $data["local_ip"] = $local_ip;
        $data["mobile_no"] = $request->mobile_no??"";
        $data["url"] = $currentURL;
        $data["activity_at"] = date("Y-m-d H:i:s");
        $data["lags_longs"] = $request->lags_longs??"";
        $data["device_id"] = $request->device_id??"";
        $data["app_version"] = $request->app_version??"";
        $data["mobile_type"] = $request->mobile_type??"";
        if(isset($request->mobile_no) && $request->mobile_no !="")
        $user = user_log::create($data);
        return $this->sendResponse(new ProjectResource($vesion), "Performed successfully");
    }

    public function bulk_sms(Request $request)
    {
        if($request->pass == "gjsdflaldfs"){
            // $users = User::where("mobile_no","9414468070")->get("mobile_no, first_name");
            // $mob_arr = array();
            // foreach($users as $user){
            //     $mob_arr[]=$user->mobile_no;
            //     $message = "प्रिय एडवाइजर n\बैंकसाथी एप्लिकेशन में कुछ नए बदलाव किए गए हैं जिसके चलते आपके एप्लिकेशन का वर्तमान वर्जन अब काम नहीं करेगा! सेवाओं का उपयोग जारी रखने के लिए कृपया एप्लिकेशन को नवीनतम  वर्जन में अप-डेट करें! अपडेट करने के लिए क्लिक करें: https://play.google.com/store/apps/details?id=com.app.banksathi \n --आपका बैंकसाथी ";
            // }

            // if(count($mob_arr) < 9999)
            // sendBulk($mob_arr, $message);
            $msg = array(
                array(
                    'number' => 9414468070,
                    'text' => rawurlencode('प्रिय एडवाइजर बैंकसाथी एप्लिकेशन में कुछ नए बदलाव किए गए हैं जिसके चलते आपके एप्लिकेशन का वर्तमान वर्जन अब काम नहीं करेगा! सेवाओं का उपयोग जारी रखने के लिए कृपया एप्लिकेशन को नवीनतम  वर्जन में अप-डेट करें! अपडेट करने के लिए क्लिक करें: https://play.google.com/store/apps/details?id=com.app.banksathi \n --आपका बैंकसाथी')
                ),
                array(
                    'number' => 9414468070,
                    'text' => rawurlencode('प्रिय sandeep बैंकसाथी एप्लिकेशन में कुछ नए बदलाव किए गए हैं जिसके चलते आपके एप्लिकेशन का वर्तमान वर्जन अब काम नहीं करेगा! सेवाओं का उपयोग जारी रखने के लिए कृपया एप्लिकेशन को नवीनतम  वर्जन में अप-डेट करें! अपडेट करने के लिए क्लिक करें: https://play.google.com/store/apps/details?id=com.app.banksathi \n --आपका बैंकसाथी')
                )
                );


            $sus["datae"] = sendBulkWithName($msg);
            return $this->sendResponse(new ProjectResource($sus), "Register successfully");
        }

    }

    public function true_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        // $user = User::where(["mobile_no"=>$request->mobile_no,"user_type"=>"advisor"])->first();
        $user = User::where("mobile_no",$request->mobile_no)
                        ->where('user_status','>','0')->first();
        if(!$user){
            $validatedData["user_type_id"] = '10';
            $validatedData["mobile_no"] = $request->mobile_no;
            $user = User::create($validatedData);
            $success['token'] =  $user->createToken($request->mobile_no)->plainTextToken;
            $res["id"] = $user->id;
            $res["user_code"] = $user->user_code;
            $success['user'] =  $res;
        }else{
            if($user->user_status == "0")
            return $this->sendError('Block Error.', array("err_msg"=>["You are blocked, Please contact to Support !!"]));

            $user->tokens()->where('tokenable_id', $user->id)->delete();
            $success['token'] =  $user->createToken($request->mobile_no)->plainTextToken;
            $res["id"] = $user->id;
            $res["user_code"] = $user->user_code;
            $success['user'] =  $res;

        }
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function login(Request $request)
    {

        // $validatedData = $request->validate([
        //     'mobile_no'=>['required', 'min:10','max:10']
        // ]);
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where("mobile_no",$request->mobile_no)->first();

        if(!$user){
            // return response()->json(' User exists ');
            // $otp = rand(1000,9999);
            $otp = 1111;
            
            $sms = sendOtp($otp,$request->mobile_no,"yes");
            if(isset($sms->status) && $sms->status == "success"){
                // $validatedData["otp"] = isset($sms->otp)?$sms->otp:$otp;
                $validatedData["otp"] = 1111;
                $validatedData["user_type_id"] = '10';
                $validatedData["mobile_no"] = $request->mobile_no;

                $user = User::create($validatedData);
                // $token = explode("|", $user->createToken($request->mobile_no)->plainTextToken);
                // $success['token'] =  $token[1];
                // $success['token'] =  $user->createToken($request->mobile_no)->plainTextToken;
                $success['otp'] =  $validatedData["otp"];
            }else{
                return $this->sendError('SMS Error.', array("err_msg"=>["SMS sending Error"]));
            }
            // $success['user'] =  $user;
        }else{
            // return response()->json(' User do not exists ');
            if($user->user_status == "0")
            return $this->sendError('Block Error.', array("err_msg"=>["You are blocked, Please contact to Support !!"]));

            if(isset($request->otp) && $request->otp != ""){
                // $user = User::where(["mobile_no"=>$request->mobile_no, "otp"=>$request->otp])->first();

                if($user->otp != $request->otp){
                    return $this->sendError('OTP Error.', array("err_msg"=>["OTP did not match !!"]));
                }else{
                    $user->tokens()->where('tokenable_id', $user->id)->delete();
                    $success['token'] =  $user->createToken($request->mobile_no)->plainTextToken;
                    // $success['token'] =  "7|K2nLTgQ8qvZxy3fRV73IW3iaMzsQYAnYBLIO9GF9";
                    $res["id"] = $user->id;
                    $res["user_code"] = $user->user_code;
                    $success['user'] =  $res;
                }
            }else{
                // $users = User::find($user->id);
                // $otp = rand(1000,9999);
                $otp = 1111;
                $sms = sendOtp($otp,$request->mobile_no,"yes");
                // $sms = new stdClass;
                // $sms->ErrorCode = "000";
                if(isset($sms->status) && $sms->status == "success"){
                    $user->otp = $otp;
                    $user->update();

                    // $user->tokens()->where('tokenable_id', $user->id)->delete();
                    // $token = explode("|", $user->createToken($request->mobile_no)->plainTextToken);
                    // $success['token'] =  $token[1];
                    // $success['token'] =  $user->createToken($request->mobile_no)->plainTextToken;
                    $success['otp'] =  NULL;
                    // $success['otp'] =  $user->otp;
                } else {
                    return $this->sendError('SMS Error.', array("err_msg"=>["SMS sending Error"]));
                }
            }

        }
        // $sms = $this->sendOtp($otp,$request->mobile_no);
        // $success['sms'] =  $sms;
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    // private function sendOtp($otp, $moble)
    // {
    //     // $api = "http://mysms.msg24.in/api/mt/SendSMS?user=demo&password=demo123&senderid=WEBSMS&channel=Promo&DCS=0&flashsms=0&number=91989xxxxxxx&text=test message&route=##
    //     $apiKey = "jY6vpE92NEinBjox0ThADw";
    //     // $api = "http://mysms.msg24.in/api/mt/SendSMS?APIKey=YouApiKey&senderid=WEBWEB&channel=Trans&DCS=0&flashsms=0&number=91989xxxxxxx&text=testmessage&route=8";
    //     $api = "http://mysms.msg24.in/api/mt/SendSMS";
    //     // $msg = "The OTP is $otp. Please don't share with anyone, generated at ".date("Y-m-d H:i:s")." and Valid for 2 minutes -BankSathi I1sXG5og7sh";
    //     $msg = "The OTP is $otp. Please don't share with anyone, generated at ".date("Y-m-d H:i:s")." and Valid for 2 minutes -BankSathi y04N7osAekq";
    //     // BSATHI
    //     // RouteId=8;
    //     $data = [
    //             "APIKey"=>$apiKey,
    //             // "user"=>"jitendradhakajpr",
    //             // "password"=>"123456",
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
    //     // $response = '{"ErrorCode":"000","ErrorMessage":"Done","JobId":"20047","MessageData":[{"Number":"91989xxxxxxx","MessageId":"mvHdpSyS7UOs9hjxixQLvw"},{"Number":"917405080952","MessageId":"PfivClgH20iG6G5R3usHwA"}]}';
    // }

    public function index(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'mobile_no' => 'required|min:10|max:10'
        // ]);

        // if($validator->fails()){
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }
        // $user = User::where("mobile_no",$request->mobile_no)->first();
        // $success['user'] =  $user;
        // return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function fcmtokenpush(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'fcm_token' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // $fcm_token="cuLoU-sqSj-dehIo50iGR2:APA91bHXlhXk9_MicsRzbGkSRcKkNCGXfU9iTmJvgzhHQiRaqzR1Ve8FM5YmS_upTQYmQVdSCgfrQc4DV_ZltCEXyRhcdCfIZyBnYVRi_AwJ0uaiJoOJ426NasTMslvH-K_eTuWDms3A";
        $user_id = $request->user_id;
        $users = User::find($user_id);
        $users->fcm_token = $request->fcm_token;
        $users->update();

        $success["msg"] = "Got It !!";
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function bank(Request $request)
    {
        // $fcm_token="cuLoU-sqSj-dehIo50iGR2:APA91bHXlhXk9_MicsRzbGkSRcKkNCGXfU9iTmJvgzhHQiRaqzR1Ve8FM5YmS_upTQYmQVdSCgfrQc4DV_ZltCEXyRhcdCfIZyBnYVRi_AwJ0uaiJoOJ426NasTMslvH-K_eTuWDms3A";
        // $title="Test Title"; $message="Test Message"; $id="20";
        // $res = sendPushNotification($fcm_token, $title, $message, $id = null);
        // return $res;
        $validator = Validator::make($request->all(), [
            'pass' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        // if($request->pass != "Bank11"){
        //     return $this->sendError('Validation Error.', array("err_msg"=>["Wrongly posted !!"]));
        // }
        if($request->pass == "sms"){
            $sms = sendOtp_new('2241',$request->mobile_no,"yes");
            $success['sms'] =  $sms;
            return $this->sendResponse(new ProjectResource($success), "Performed successfully");
        }
        // $user = User::where("mobile_no",$request->mobile_no)
        //             ->Where(function ($queri) {
        //                 $queri->where('user_type', 'advisor')
        //                     ->orwhere('user_type', 'nh')
        //                     ->orwhere('user_type', 'zh')
        //                     ->orwhere('user_type', 'sh')
        //                     ->orwhere('user_type', 'ch')
        //                     ->orwhere('user_type', 'cth')
        //                     ->orwhere('user_type', 'bdm');
        //             })->first();

        // $success["user"] = $user;
        // $success["testdata"] = "Working ".date("Y-m-d H:i:s");
        // return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function update_account(Request $request)
    {
        $user_id = $request->user_id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|unique:users,email,$user_id",
            'pincode' => "required|min:6|max:6",
            'user_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if(isset($request->referral_code) && $request->referral_code != ""){
            $referral_code = explode("/",$request->referral_code);
            if(isset($referral_code[1])){
                return $this->sendError('Referral Code Validation.', array("err_msg"=>["Please correct code without slace."]));
            }
            $refuser = User::where("user_code",$request->referral_code)->first();
            if(!$refuser){
                return $this->sendError('Referral Code Validation.', array("err_msg"=>["InValid Referral Code, pleae leave blank if you don't have refferal code !!"]));
            }
        }

        $u_code = DB::select("select SUBSTRING(user_code,7,LENGTH(user_code)) as code from users where LEFT(user_code, 6) = ? ", [$request->pincode]);
        $codeArr=array();
        if(!empty($u_code))
        foreach($u_code as $val){
            $codeArr[] = $val->code;
        }

        if(!empty($codeArr)){
            $new_count=max($codeArr)+1;
            $user_code = ($new_count > 9)? $request->pincode . $new_count : $request->pincode . "0" . $new_count;
        }else $user_code = $request->pincode."01";
        $pass = Hash::make($user_code);

        $users = User::find($user_id);
        $users->first_name = $request->first_name;
        $users->last_name = $request->last_name;
        $users->email = $request->email;
        if(is_null($users->user_code)){
            $users->user_code = $user_code;
        }
        $users->password = $pass;

        if(isset($request->referral_code) && $request->referral_code != ""){
            $users->referral_code = $request->referral_code??"";
            $users->referred_by = $refuser->id??NULL;
        }

        $users->update();

        $pincode = pincode::where("pincode",$request->pincode)->first();
        if(!$pincode)
            return $this->sendError('Pincode Validation.', array("err_msg"=>["Pincode does not exist !!"]));

        if(isset($users->address->id)){
            $addresses = Address::find($users->address->id);
            $addresses->user_id = $user_id;
            $addresses->pincode_id = $pincode->id;
            $addresses->city_id = $pincode->city_id;
            $addresses->state_id = $pincode->city->state_id??NULL;
            $addresses->is_current = "y";
            $addresses->update();
        }else{
            $addresses = new Address();
            $addresses->user_id = $user_id;
            $addresses->pincode_id = $pincode->id;
            $addresses->city_id = $pincode->city_id;
            $addresses->state_id = $pincode->city->state_id??NULL;
            $addresses->is_current = "y";
            $addresses->save();
        }

        // $users->addresses = $addresses;
        // $res["host"] = request()->getHost();
        $res["id"] = $users->id;
        $res["user_code"] = $users->user_code;
        $success['user'] =  $res;

        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function getpincode(Request $request)
    {
        $pincodes = [];
        if($request->has('search')){
            $search = $request->search;
            $pincodes = pincode::select("id", "pincode")->orderBy('pincode')
            		->where('pincode', 'LIKE', "$search%")
            		->get();
        }else{
            $pincodes = pincode::orderBy('pincode')->get(['id','pincode']);
        }
        // $pincodes = pincode::all(['id','pincode']);
        return $this->sendResponse(new ProjectResource($pincodes), "Performed successfully");
    }

    public function getuser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user_id=$request->user_id;
        $user = User::find($user_id);
        if(isset($user->relbank->upload_doc))
        $user->relbank->upload_doc = ($user->relbank->upload_doc != "")?url("storage/advisors/$user_id/".$user->relbank->upload_doc):"";
        else $user->relbank = array();
        $user->upload_qual_doc = ($user->upload_qual_doc != "")?url("storage/advisors/$user_id/".$user->upload_qual_doc):"";
        $user->upload_pan_no = ($user->upload_pan_no != "")?url("storage/advisors/$user_id/".$user->upload_pan_no):"";
        $user->upload_gst_no = ($user->upload_gst_no != "")?url("storage/advisors/$user_id/".$user->upload_gst_no):"";
        if (strpos($user->profile_photo, 'http') === false)
            $user->profile_photo = ($user->profile_photo != "")?url("storage/advisors/$user_id/".$user->profile_photo):"";
        if(isset($user->address->id_doc_front)){
            $user->address->id_doc_front = ($user->address->id_doc_front != "")?url("storage/advisors/$user_id/".$user->address->id_doc_front):"";
            $user->address->id_doc_back = ($user->address->id_doc_back != "")?url("storage/advisors/$user_id/".$user->address->id_doc_back):"";
        }else{
            $user->address = array();
        }

        $user_type = isset($user->usertype->title)?ucfirst($user->usertype->title):"";

        $user->user_type = $user_type;
        $data["users"] = $user;
        $data["address"] = $user->address;
        $data["relbank"] = $user->relbank;
        $data["rel_fins"] = rel_fin::where("user_id", $user_id)->get(["fin_product_id"]);

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function getcity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $cities = city::where("state_id",$request->state_id)->get(['id','city_name']);
        return $this->sendResponse(new ProjectResource($cities), "Performed successfully");
    }

    public function myteam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_code' => 'required',
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        // $user_id= $request->user_id;
        // $users = User::where("referral_code",$request->user_code)
        //                 ->where('referred_by',$request->user_id)
        //                 ->where('user_status','>','0')
        //                 ->get(['id','first_name','last_name','user_status','profile_photo']);
        // $res = array();
        // if(!empty($users))
        // foreach($users as $user){
        //     $user->profile_photo = ($user->profile_photo != "")?url("storage/advisors/$user->id/".$user->profile_photo):"";
        //     $user->designation = "Advisor";
        //     $res[] = $user;
        // }

        $users = User::where("referred_by", $request->user_id)->get("select  id, first_name, last_name, user_status, user_type, profile_photo");

        // $users = DB::select("select  id, first_name, last_name, user_status, user_type, profile_photo
        // from    (select * from users WHERE user_status > '0' AND (user_type = 'advisor' OR  user_type = 'nh' OR  user_type = 'zh' OR  user_type = 'sh' OR  user_type = 'ch' OR  user_type = 'cth' OR  user_type = 'bdm')
        //          order by referred_by, id) products_sorted,
        //         (select @pv := ?) initialisation
        // where   find_in_set(referred_by, @pv)
        // and     length(@pv := concat(@pv, ',', id))", [$request->user_id]);

        $res = array();$res_user = array();
        if(!empty($users))
        foreach($users as $user){
            $res_user['id'] = $user->id;
            $res_user['first_name'] = $user->first_name;
            $res_user['last_name'] = $user->last_name;
            $res_user['user_status'] = $user->user_status;

            $user_type = isset($user->usertype->title)?ucfirst($user->usertype->title):"";

            $res_user['profile_photo'] = ($user->profile_photo != "")?url("storage/advisors/$user->id/".$user->profile_photo):"";

            $res_user['designation'] = $user_type;
            $res[] = $res_user;
        }

        return $this->sendResponse(new ProjectResource($res), "Performed successfully");
    }

    public function getcitystate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pincode_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $pincode = pincode::find($request->pincode_id);
        // $pincode->city->state_id
        if(!$pincode){
            return $this->sendError('Pincode Error.', array("err_msg"=>["Pincode does not exist !!"]));
        }
        $cities = city::where("state_id",$pincode->city->state_id)->get(['id','city_name','state_id']);
        $data['cities']=$cities;
        $data['citystate_id']=['city_id'=>$pincode->city->id, 'state_id'=>$pincode->city->state_id];
        $data['pincode']=$pincode->pincode;
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function getDropDowns()
    {
        $data['banks'] = bank::all(['id','bank_title']);
        $data['states'] = state::where("country_id",'1')->get(["state_name","id"]);
        $data['pos_incomes'] = pos_income::all(['id','title']);
        $data['fin_products'] = fin_product::all(['id','product_name']);
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }

    public function personal_details(Request $request)
    {
        $user_id=$request->user_id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|unique:users,email,$user_id",
            'mobile_no' => "required|unique:users,mobile_no,$user_id|min:10|max:10",
            'pincode_id' => "required",
            'user_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $users = User::find($user_id);
        $users->are_you = $request->are_you;
        $users->firm_name = $request->firm_name??"";
        $users->first_name = $request->first_name;
        $users->last_name = $request->last_name;
        $users->email = $request->email;
        $users->mobile_no = $request->mobile_no;
        $users->dob = date('Y-m-d',strtotime($request->dob));
        $users->nominee_name = $request->nominee_name;
        $users->nominee_relation = $request->nominee_relation;
        $users->nominee_dob = date('Y-m-d',strtotime($request->nominee_dob));
        $users->gender = $request->gender;
        $users->update();
        // $pincode = pincode::where("pincode",$request->pincode)->first();
        if(isset($users->address->id)){
            $addresses = Address::find($users->address->id);
            $addresses->user_id = $user_id;
            $addresses->add1 = $request->add1;
            $addresses->add2 = $request->add2;
            $addresses->pincode_id = $request->pincode_id;
            $addresses->city_id = $request->city_id;
            $addresses->state_id = $request->state_id;
            $addresses->is_current = $request->is_current??"y";
            // $addresses->add_proof = $request->add_proof;
            // $addresses->add_proof_no = $request->add_proof_no;
            // if($id_doc_front != "") $addresses->id_doc_front = $id_doc_front;
            // if($id_doc_back != "") $addresses->id_doc_back = $id_doc_back;
            $addresses->update();
        }else{
            $addresses = new Address();
            $addresses->user_id = $user_id;
            $addresses->add1 = $request->add1;
            $addresses->add2 = $request->add2;
            $addresses->pincode_id = $request->pincode_id;
            $addresses->city_id = $request->city_id;
            $addresses->state_id = $request->state_id;
            $addresses->is_current = $request->is_current??"y";
            // $addresses->add_proof = $request->add_proof??"d";
            // $addresses->add_proof_no = $request->add_proof_no;
            // $addresses->id_doc_front = $id_doc_front;
            // $addresses->id_doc_back = $id_doc_back;
            $addresses->save();
        }

        // $users->addresses = $addresses;
        $success['user'] =  $users;

        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function bank_details(Request $request)
    {
        $user_id=$request->user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => "required",
            'bank_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $path = 'public/advisors/'.$user_id;
        $upload_doc = "";
        if($request->hasFile('upload_doc')){
            $upload_doc = substr(str_replace(" ","-",$request->file('upload_doc')->getClientOriginalName()),-10);
            $upload_doc = time().$upload_doc;
            $request->file('upload_doc')->storeAs($path, $upload_doc);
        }
        $users = User::find($user_id);
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
                $relBanks->upload_doc = ($relBanks->upload_doc != "")?url("storage/advisors/$user_id/".$relBanks->upload_doc):"";
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
                $relBanks->upload_doc = ($relBanks->upload_doc != "")?url("storage/advisors/$user_id/".$relBanks->upload_doc):"";
            }
        }
        // $users->relBanks = $relBanks;
        $success['relBanks'] =  $relBanks;

        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function edu_details(Request $request)
    {
        $user_id=$request->user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $path = 'public/advisors/'.$user_id;
        $upload_qual_doc = "";
        if($request->hasFile('upload_qual_doc')){
            $upload_qual_doc = substr(str_replace(" ","-",$request->file('upload_qual_doc')->getClientOriginalName()),-10);
            $upload_qual_doc = time().$upload_qual_doc;
            $request->file('upload_qual_doc')->storeAs($path, $upload_qual_doc);
        }
        $users = User::find($user_id);
        $users->qualification = $request->qualification;
        if($upload_qual_doc != "") $users->upload_qual_doc = $upload_qual_doc;
        $users->update();
        $users->upload_qual_doc = ($users->upload_qual_doc != "")?url("storage/advisors/$user_id/".$users->upload_qual_doc):"";

        $success['user'] =  $users;
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function pro_details(Request $request)
    {
        $user_id=$request->user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $users = User::find($user_id);
        $users->pos_income_id = $request->pos_income_id;
        $users->total_fn_yr = $request->total_fn_yr;
        $users->total_fn_month = $request->total_fn_month;
        $users->office_space = $request->office_space;
        // $users->did_sell = $request->did_sell;
        $users->pos_licence = $request->pos_licence;
        $users->total_bus_anum = $request->total_bus_anum;
        $users->update();

        rel_fin::where('user_id', $user_id)->delete();
        $relFin = [];
        $did_sell = [];

        $did_sell_res = json_decode($request->did_sell);
        // print_r($a);die;

        if(!empty($did_sell_res))
        foreach($did_sell_res as $value){
            if($value != "")
            $relFin[] = [
                'user_id' => $user_id,
                'fin_product_id' => $value
            ];
        }
        if(!empty($relFin)) rel_fin::insert($relFin);
        $success["did_sell"] = $did_sell;
        $success['user'] =  $users;
        // $success['ddddd'] =  $relFin;
        // print_r($relFin);die;
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function kyc_details(Request $request)
    {
        $user_id=$request->user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $path = 'public/advisors/'.$user_id;
        $upload_pan_no = "";
        $upload_gst_no = "";
        $id_doc_front = "";
        $id_doc_back = "";

        if($request->hasFile('upload_pan_no')){
            $upload_pan_no = substr(str_replace(" ","-",$request->file('upload_pan_no')->getClientOriginalName()),-10);
            $upload_pan_no = time().$upload_pan_no;
            $request->file('upload_pan_no')->storeAs($path, $upload_pan_no);
        }
        if($request->hasFile('upload_gst_no')){
            $upload_gst_no = time().$request->file('upload_gst_no')->getClientOriginalName();
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

        $users = User::find($user_id);
        $users->pan_no = $request->pan_no;
        if($upload_pan_no != "") $users->upload_pan_no = $upload_pan_no;
        $users->gst_no = $request->gst_no;
        if($upload_gst_no != "") $users->upload_gst_no = $upload_gst_no;
        $users->update();
        $users->upload_pan_no = ($users->upload_pan_no != "")?url("storage/advisors/$user_id/".$users->upload_pan_no):"";
        $users->upload_gst_no = ($users->upload_gst_no != "")?url("storage/advisors/$user_id/".$users->upload_gst_no):"";

        if(isset($users->address->id)){
            $addresses = Address::find($users->address->id);
            $addresses->user_id = $user_id;
            $addresses->add_proof = $request->add_proof;
            $addresses->add_proof_no = $request->add_proof_no;
            if($id_doc_front != "") $addresses->id_doc_front = $id_doc_front;
            if($id_doc_back != "") $addresses->id_doc_back = $id_doc_back;
            $addresses->update();
            $addresses->id_doc_front = ($addresses->id_doc_front != "")?url("storage/advisors/$user_id/".$addresses->id_doc_front):"";
            $addresses->id_doc_back = ($addresses->id_doc_back != "")?url("storage/advisors/$user_id/".$addresses->id_doc_back):"";
        }else{
            $addresses = new Address();
            $addresses->user_id = $user_id;
            $addresses->add_proof = $request->add_proof??"d";
            $addresses->add_proof_no = $request->add_proof_no;
            $addresses->id_doc_front = $id_doc_front;
            $addresses->id_doc_back = $id_doc_back;
            $addresses->save();
            $addresses->id_doc_front = ($addresses->id_doc_front != "")?url("storage/advisors/$user_id/".$addresses->id_doc_front):"";
            $addresses->id_doc_back = ($addresses->id_doc_back != "")?url("storage/advisors/$user_id/".$addresses->id_doc_back):"";
        }
        $success["addresses"] = $addresses;
        $success['user'] =  $users;
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }

    public function profile_pic(Request $request)
    {
        $user_id=$request->user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $path = 'public/advisors/'.$user_id;
        $profile_photo = "";
        if($request->hasFile('profile_photo')){
            $profile_photo = substr(str_replace(" ","-",$request->file('profile_photo')->getClientOriginalName()),-10);
            $profile_photo = time().$profile_photo;
            $request->file('profile_photo')->storeAs($path, $profile_photo);
        }

        $users = User::find($user_id);
        if($profile_photo != ""){
            $users->profile_photo = $profile_photo;
            $users->update();
        }else{
            return $this->sendError('Profile Photo Error.', array("err_msg"=>["No photo uploaded"]));
        }
        $success['user'] =  $users->id;
        return $this->sendResponse(new ProjectResource($success), "Performed successfully");
    }


    /**
    * To Store mpin for specific user
    *
    * @param object $request
    * @return "Does not return anything."
    */
    public function storeMpin(Request $request)
    {
        $this->validate($request, [
            'mpin' => 'required',
            "user_id" => "required"
        ]);
            
        $encryptedMpin = Hash::make($request->mpin);
        
        // Create Listing
        $user = User::find($request->user_id);
        $user->is_mpin = "yes";
        $user->password = $encryptedMpin;
        $user->update();

        // $user = User::create($data);
        return $this->sendResponse(new ProjectResource($user), "MPIN saved successfully.");
    }

    /**
    * To Store mpin for specific user
    *
    * @param object $request
    * @return "Does not return anything."
    */
    public function verifyMpin(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'mpin' => 'required',
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $mpin = $request->mpin;

        $user = User::where(['id' => $request->user_id])->first();
            
        // Check if mpin matches
        if (Hash::check($mpin, $user->password)) {
            return $this->sendResponse(new ProjectResource($user), "MPIN verified successfully.");
        } else {
            return $this->sendError([], "Opps! MPIN does not match.");
        }

    }

    public function changeMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10|max:10',
            'user_id' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
      
        $userRow = User::where([
                    'id' => $request->input('user_id'),
                    'otp' => $request->input('otp')
                    ])->first();

        $userRow->mobile_no = $request->input('mobile_no');
        $userRow->save();

        return $this->sendResponse($request->all(),'Request successfully done.');

    }

    /*
    **
    * To Store mpin for specific user
    *
    * @param object $request
    * @return "Does not return anything."
    */
    public function walletHistory(Request $request)
    {

        $user = auth()->user();
        
        $userid = auth()->user()->id;
        
        // return response()->json($userid);
        // $userid = 4; // test code
        
        $walletCount = Wallet::where(['user_id' => $userid,'status' => 1])->count();
        if ($walletCount==0) {
            return $this->sendError("Opps! Wallet is empty.", []);
        }

        $wallet = Wallet::select('id','premium_without_gst','gst','total_premium','payout_basis','commissionable_amount','percentage','amount','payout_to_lg','status','created_at','updated_at')->where(['user_id' => $userid, 'status' => 1])->paginate(10);
        // $wallet = Wallet::where(['user_id' => $userid, 'status' => 1])->paginate(10);
        return $this->sendResponse(new ProjectResource($wallet), "Users Wallet Details.");
    }

    public function userTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        dd($request->all());
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
      
        $userRow = User::where([
                    'id' => $request->input('user_id'),
                    'otp' => $request->input('otp')
                    ])->first();

        $userRow->mobile_no = $request->input('mobile_no');
        $userRow->save();

        return $this->sendResponse($request->all(),'Request successfully done.');

    }



    /**
     *   new lead
     *   customer not responding
     *   policy issued-under review
     *   policy issued
     *   Shared Quotation
     *   Payment done-policy pending
     *   Policy cancelled by customer
     *   Payment link shared-payment due
     *  
     */

    //  [1 => 'new lead', 2 => 'customer not responding', 3 => 'policy issued-under review', 4 => 'policy issued', 5 => 'shared quotation', 6 => 'payment done policy pending',7 => 'policy cancelled by customer',8 => 'payment link shared payment due'];
    
}
