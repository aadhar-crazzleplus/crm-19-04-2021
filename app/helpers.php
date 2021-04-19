<?php
use Illuminate\Support\Facades\Http;
function sendPushNotification($fcm_token, $title, $message, $id = null) {

    $your_project_id_as_key = "AAAA73UD0xk:APA91bG3G4qaqA2JwSF8zJKSC-iNGuW1zgRhHVyY8QsIkKBxZDA16I8VPgw5Gfnsj6YQvSuO_I3zLwDkjS34eNXP8COF_ecsTuZM0WcXZrIdhH75mo8GFRObFoBI9U2rA0yHh5-56LEW";

    $url = "https://fcm.googleapis.com/fcm/send";
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'authorization' => 'key=' . $your_project_id_as_key
        ])->withOptions([
            'verify' => false,
            'protocols' => ['http', 'https'],
        ])->post($url, [
            "to" => $fcm_token,
            "notification" => [
                "title" => $title,
                "text" => $message
            ],
            "data" => [
                "id" => "20",
                "title" => $title,
                "description" => $message,
                "text" => $message,
                "is_read" => 0
            ]
        ]);

        return $response;

}

function sendOtp($otp, $moble, $isOwn = "no",$app_code=""){

    $host = request()->getHost();
    if($host == "devbanksathi.test" || $host == "dev.banksathi.com"){
        $sms = new stdClass;
        $sms->status = "success";
        $sms->otp=1111;
        return $sms;
    }

    $code="";
    if($isOwn=="yes"){
        $code = $app_code;
    }
    $apiKey = urlencode('O36m0bM5UDo-rZ4lDHlYbEaEOCLC4CUJ8gsnRzaYqV');
    // Message details
    // Maximum of 10,000 numbers and error code 33 will be returned if exceeded.
    $numbers = array($moble);
    // if(is_array($moble))
    //     $numbers = $moble;
    // else
    //     $numbers = array($moble);

    $sender = urlencode('BSAATI');
    // This parameter should be no longer than 918 characters
    $message = rawurlencode("The OTP is $otp. Please don't share with anyone, generated at ".date("d-m-Y H:i:s")." and Valid for 2 minutes -BankSathi ".$code);
    $numbers = implode(',', $numbers);

    // Prepare data for POST request
    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

    // Send the POST request with cURL
    $ch = curl_init('https://api.textlocal.in/send/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $sms = json_decode($response);
    if(isset($sms->balance) && $sms->balance == 100){
        sendBalSms($sms->balance);
    }
    return $sms;
}

function get_veh_api($regn_no)
{
    // $api = '{"id":"542bbcd3-9861-4da3-a4c3-0bd07debeaf1","env":1,"request_timestamp":"2021-02-19 15:58:13:298 +05:30","response_timestamp":"2021-02-19 15:58:18:056 +05:30","transaction_status":1,"result":{"state_cd":"RJ","rc_regn_no":"RJ45CG9333","rc_regn_dt":"12-Jun-2018","rc_chasi_no":"MAKGM851CJ4303221","rc_eng_no":"N15A16212294","rc_vh_class_desc":"Motor Car(LMV)","rc_maker_desc":"HONDA CARS INDIA LTD","rc_maker_model":"CITY 1.5 V MT (I-DTEC)","rc_body_type_desc":"SALOON","rc_fuel_desc":"DIESEL","rc_color":"WHITE ORCHID PEARL","rc_owner_name":"HARI RAM","rc_f_name":"TEJA RAM","rc_permanent_address":"F-3 FIRST FLOOR PLOT NO 141 , SHRI DADU DAYAL NAGAR,BLOCK KALYANPURA SANGANER, Jaipur -302029","rc_present_address":"F-3 FIRST FLOOR PLOT NO 141 , SHRI DADU DAYAL NAGAR,BLOCK KALYANPURA SANGANER, Jaipur -302029","rc_fit_upto":"11-Jun-2033","rc_tax_upto":"LTT","rc_norms_desc":"BHARAT STAGE IV","rc_insurance_comp":"New India Assurance  Co. Ltd.","rc_insurance_policy_no":"31030031200300089900","rc_insurance_upto":"29-May-2021","rc_registered_at":"JAIPUR (JHALANA) RTO, Rajasthan","rc_manu_month_yr":"3/2018","rc_unld_wt":"1148","rc_gvw":"1523","rc_no_cyl":"4","rc_cubic_cap":"1498.0","rc_seat_cap":"5","rc_sleeper_cap":"0","rc_stand_cap":"0","rc_wheelbase":"2600","rc_owner_sr":"1","rc_mobile_no":null,"rc_vch_catg":"LMV","rc_pucc_upto":"NA","rc_pucc_no":"NA","rc_financer":"HDFC BANK LTD","rc_blacklist_status":"NA","rc_noc_details":"NA","rc_status":"ACTIVE","rc_status_as_on":"19-Feb-2021","stautsMessage":"OK","rc_non_use_status":null,"rc_non_use_from":null,"rc_non_use_to":null,"rc_permit_issue_dt":"","rc_permit_no":"","rc_permit_type":"","rc_permit_valid_from":"","rc_permit_valid_upto":"","rc_np_issued_by":"","rc_np_no":"","rc_np_upto":""},"response_msg":"Success","response_code":"101"}';
    // return $api;
    // $url = 'https://preprod.aadhaarapi.com/verify-rc-lite';
    $url = 'https://preprod.aadhaarapi.com/verify-rc/v3';
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'qt_api_key' => '1620560e-aa21-4932-a712-3c3f09e68548',
        'qt_agency_id' => '5932f092-e0c9-4a5c-9692-cbe720cac915'
    ])->withOptions([
        'verify' => false,
        'protocols' => ['http', 'https'],
    ])->post($url, [
        "reg_no" => $regn_no,
        "consent" => "Y",
        "consent_text" => "Yes, I am requesting to fetch my vehicle informations."
    ]);

    return $response;
}

function sendBulk($moble, $message){

    $apiKey = urlencode('O36m0bM5UDo-rZ4lDHlYbEaEOCLC4CUJ8gsnRzaYqV');
    // Message details
    if(is_array($moble))
        $numbers = $moble;
    else
        $numbers = array($moble);

    $sender = urlencode('BSAATI');
    $message = rawurlencode($message);
    $numbers = implode(',', $numbers);

    // Prepare data for POST request
    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

    // Send the POST request with cURL
    $ch = curl_init('https://api.textlocal.in/send/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $sms = json_decode($response);
    if(isset($sms->balance) && $sms->balance == 100){
        sendBalSms($sms->balance);
    }
    return $sms;
}
function sendBulkWithName($message){

    $apiKey = urlencode('O36m0bM5UDo-rZ4lDHlYbEaEOCLC4CUJ8gsnRzaYqV');
    $messages = array(
        // Put parameters here such as sender, force or test
        'sender' => "BSAATI",
        'messages' => $message
    );

    // Prepare data for POST request
    $data = array(
        'apikey' => $apiKey,
        'data' => json_encode($messages)
    );

    // Send the POST request with cURL
    $ch = curl_init('https://api.textlocal.in/bulk_json/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // echo $response;
    $sms = json_decode($response);
    // if(isset($sms->balance) && $sms->balance == 100){
    //     sendBalSms($sms->balance);
    // }
    return $sms;
}

function sendBalSms($bal){
    $apiKey = urlencode('O36m0bM5UDo-rZ4lDHlYbEaEOCLC4CUJ8gsnRzaYqV');
    // Message details
    $numbers = array(9414468070,9602029333,7568925133);
    $sender = urlencode('BSATHe');
    $message = rawurlencode("Your SMS provider Balance reached below $bal, please recharge immediately to avoid hassles in the service. --Sandeep Kaler (CTO BankSathi)");
    $numbers = implode(',', $numbers);
    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
    $api = "https://api.textlocal.in/send/";
    $res = Http::get($api,$data);
    $sms = json_decode($res);
    // if(isset($sms->status) && $sms->status == "success"){
    //     $sms->ErrorCode = "000";
    // }
    return $sms;
}

function sendOtp_new($otp, $moble, $isOwn = "no"){
    // $sms = new stdClass;
    // $sms->ErrorCode = "000";
    // $sms->otp="1111";
    // return $sms;
    $code="";
    if($isOwn=="yes"){
        $code = "y04N7osAekq";
    }
    $apiKey = urlencode('O36m0bM5UDo-rZ4lDHlYbEaEOCLC4CUJ8gsnRzaYqV');
    // Message details
    $numbers = array($moble);
    $sender = urlencode('BSAATI');
    $message = rawurlencode("The OTP is $otp. Please don't share with anyone, generated at ".date("d-m-Y H:i:s")." and Valid for 2 minutes -BankSathi ".$code);
    $numbers = implode(',', $numbers);
    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
    $api = "https://api.textlocal.in/send/";
    $res = Http::get($api,$data);
    $sms = json_decode($res);
    if(isset($sms->balance) && $sms->balance == 100){
        sendBalSms($sms->balance);
    }
    return $sms;
}

function sendOtp_old($otp, $moble, $isOwn = "no"){
    // $sms = new stdClass;
    // $sms->ErrorCode = "000";
    // $sms->otp="1111";
    // return $sms;

    $code="";
    if($isOwn=="yes"){
        $code = "y04N7osAekq";
    }
    $apiKey = "jY6vpE92NEinBjox0ThADw";
    $api = "http://mysms.msg24.in/api/mt/SendSMS";
    $msg = "The OTP is $otp. Please don't share with anyone, generated at ".date("Y-m-d H:i:s")." and Valid for 2 minutes -BankSathi ".$code;
    $data = [
            "APIKey"=>$apiKey,
            "senderid"=>"BSATHI",
            "channel"=>"Trans",
            "number"=>$moble,
            "text"=>$msg,
            "route"=>"8",
            "DCS"=>"0",
            "flashsms"=>"0"
        ];
    $res = Http::get($api,$data);
    $sms = json_decode($res);
    return $sms;
}

function comError($error, $errorMessages = [], $code = 404)
{
    $response = [
        'code' => $code,
        'success' => false,
        'message' => $error,
    ];


    if(!empty($errorMessages)){
        $response['data'] = $errorMessages;
    }


    return response()->json($response, $code);
}
?>
