<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function crmLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('admin')->user();
            
            // Set session here.
            $user_type = Auth::guard('admin')->user()->user_type_id;
            $permissions = Permission::where(["user_type" => $user_type])->get()->makeHidden(['id','user_type','created_at','updated_at'])->toArray();

            $email = Auth::guard('admin')->user()->email;
            $mobile_no = Auth::guard('admin')->user()->mobile_no;
            $usrDetails = [
                "user_type" => $user_type,
                "email" => $email,
                "mobile_no" => $mobile_no,
            ];
            
            $sessionArray = array(
                'userDetails' =>  $usrDetails,
                'permissions' =>  $permissions
            );
            
            
            $request->session()->put('sessionArray', $sessionArray);

            return redirect()->intended('/dashboard');
        }
        return redirect()->back()->withInput($request->only('email'))
                ->with('error','Login failed, please try again!');
    }

    public function crmLogout(Request $request)
    {
        Auth::logout();
        return redirect('/')->with('status','You have been logged out!');
    }
}
