<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignatureHandle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // if (empty($_SERVER['HTTPS']))
        // {
        //     return comError('Update APP', array("err_msg"=>["Please update your application !!"]));
        // }

        // else
        // {
        //     // echo 'http is enabled'."\n";
        // }
            
        if ($request->hasHeader('signature') && $request->hasHeader('time') && $request->hasHeader('mobile')) {
            $sig = $request->header('signature');
            $time = $request->header('time');
            $usr_mob = $request->header('mobile');
            $md = md5($time."FAJAdtVMtB6FxmsOhuxkzzRvTQMKUZon".$time.$usr_mob.$time);
            $cusSig = md5($md).$md;
            $currentURL = $request->url();
            if($cusSig != $sig){
                // $data["user_id"] = $request->user_id??"";
                $data["ip"] = $request->ip();
                // $data["mobile_no"] = $request->mobile_no??"";
                $data["mobile_tem"] = $usr_mob??"";
                $data["signature"] = $sig??"";
                $data["time"] = $time??"";
                $data["url"] = $currentURL??"";
                $data["activity_at"] = date("Y-m-d H:i:s");
                // $data["lags_longs"] = $request->lags_longs??"";
                // $data["device_id"] = $request->device_id??"";
                // $data["app_version"] = $request->app_version??"";
                // $data["mobile_type"] = $request->mobile_type??"";
                $user = DB::insert('insert into user_logs (`ip`, `mobile_tem`, `signature`, `time`, `url`, `activity_at`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [$data["ip"], $data["mobile_tem"], $data["signature"], $data["time"], $data["url"], $data["activity_at"], $data["activity_at"], $data["activity_at"]]);
                return comError('Unauthorized Error.', array("err_msg"=>["you are not permitted !"]));
            }else{
                $data["ip"] = $request->ip();
                $data["mobile_tem"] = $usr_mob??"";
                $data["signature"] = $sig??"";
                $data["time"] = $time??"";
                $data["url"] = $currentURL??"";
                $data["activity_at"] = date("Y-m-d H:i:s");
                $user = DB::insert('insert into user_logs (`ip`, `mobile_tem`, `signature`, `time`, `url`, `activity_at`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [$data["ip"], $data["mobile_tem"], $data["signature"], $data["time"], $data["url"], $data["activity_at"], $data["activity_at"], $data["activity_at"]]);
            }
        }else{
            return comError('Unauthorized Error.', array("err_msg"=>["you are not permitted !!"]));
        }

        return $next($request);
    }
}
