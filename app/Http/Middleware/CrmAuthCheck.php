<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CrmAuthCheck
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
        $userTypeId = array(1,2,3,4,5,6,7,8,9);
        $segment = $request->segment(1);
        $sessionArray = Session::get('sessionArray');
        
        $sessionArray = isset($sessionArray) ? $sessionArray : array() ;
        
        $data = $request->session()->all();
        $userType = isset($sessionArray['userDetails']['user_type']) ? $sessionArray['userDetails']['user_type'] : null ;
        
        $request->session()->put('sessionData', $sessionArray);
        
        if (($userType!=1) && $segment==="crm-permission") {
            abort(401, 'Unauthorized access.');
        }

        if(isset(auth("admin")->user()->user_type_id) && in_array(auth("admin")->user()->user_type_id, $userTypeId)){
            return $next($request);
        }

        return redirect("login")->with("error","Oops!! you don't have access to this area.");
    }
}
