<?php

namespace App\Http\Controllers\crm;

use App\Models\Permission;
use App\Models\user_type;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;


class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // die(' index ');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['permissions'] = Permission::all('id','user_type','module','permissions')->toArray();
        $data['crm_modules'] = Config::get('constants.crm_modules');
        $data['user_type'] = user_type::where('id','!=',1)->pluck('title','id');
        return view('crm.permissions',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return "Store values doesn't return any thing."
     */
    public function store(Request $request)
    {
        
        if ($request->input('admin')!=NULL) {
            $admin = $request->input('admin');
            $adminPermissions = implode(",",$admin);
        } else{
            $adminPermissions = "0,0,0,0";
        }
        
        if ($request->input('advisor')!=NULL) {
            $advisor = $request->input('advisor');
            $advisorPermissions = implode(",",$advisor);
        } else {
            $advisorPermissions = "0,0,0,0";
        }

        if ($request->input('loan')!=NULL) {
            $loan = $request->input('loan');
            $loanPermissions = implode(",",$loan);
        } else {
            $loanPermissions = "0,0,0,0";
        }

        if ($request->input('insurance')!=NULL) {
            $insurance = $request->input('insurance');
            $insurancePermissions = implode(",",$insurance);
        } else {
            $insurancePermissions = "0,0,0,0";
        }

        if ($request->input('creditCard')!=NULL) {
            $creditCard = $request->input('creditCard');
            $creditCardPermissions = implode(",",$creditCard);
        } else {
            $creditCardPermissions = "0,0,0,0";
        }

        if ($request->input('socialCard')!=NULL) {
            $socialCard = $request->input('socialCard');
            $socialCardPermissions = implode(",",$socialCard);
        } else {
            $socialCardPermissions = "0,0,0,0";
        }

        if ($request->input('socialBanner')!=NULL) {
            $socialBanner = $request->input('socialBanner');
            $socialBannerPermissions = implode(",",$socialBanner);
        } else {
            $socialBannerPermissions = "0,0,0,0";
        }

        if ($request->input('notification')!=NULL) {
            $notification = $request->input('notification');
            $notificationPermissions = implode(",",$notification);
        } else {
            $notificationPermissions = "0,0,0,0";
        }

        if ($request->input('payout')!=NULL) {
            $payout = $request->input('payout');
            $payoutPermissions = implode(",",$payout);
        } else {
            $payoutPermissions = "0,0,0,0";
        }

        $userType = $request->input('userType');

        $userTypeCount = Permission::where('user_type', $userType)->count();
        // echo $userTypeCount; die;

        // If User already have entries.
        if ($userTypeCount > 0) {
            
            // Only Super Admin can Change the perission for admin.
            if ($userType=="1") {
                // Admin Module
                if ($request->input('admin')!=NULL) {
                    $adminRowCount = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'admin']])->count();

                    if ($adminRowCount > 0) {
                        $adminRow = Permission::where([
                            ['user_type', '=' , $userType],
                            ['module', '=' , 'admin']])->first();
                        $adminRow->permissions = $adminPermissions;
                        $adminRow->save();
                    } else {
                        $permission = new Permission;
                        $permission->user_type = $userType;
                        $permission->module = 'admin';
                        $permission->permissions = $adminPermissions;
                        $permission->save();
                    }
                } else {
                    $adminRowCount = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'admin']])->count();

                    if ($adminRowCount > 0) {
                        $adminRow = Permission::where([
                            ['user_type', '=' , $userType],
                            ['module', '=' , 'admin']])->first();

                        $adminRow->permissions = $adminPermissions;
                        $adminRow->save();
                    }
                }
            }
 

            // Advisor module
            if ($request->input('advisor')!=NULL) {
                $advisorRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'advisor']])->count();
                
                if ($advisorRowCount > 0) {
                    $advisorRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'advisor']])->first();

                    $advisorRow->permissions = $advisorPermissions;
                    $advisorRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'advisor';
                    $permission->permissions = $advisorPermissions;
                    $permission->save();
                }
            } else {
                $advisorRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'advisor']])->first();

                $advisorRow->permissions = $advisorPermissions;
                $advisorRow->save();
            }

            // Loan module
            if ($request->input('loan')!=NULL) {
                // DB::enableQueryLog();
                $loanRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'loan']])->count();
                
                if ($loanRowCount > 0) {
                    $loanRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'loan']])->first();

                    $loanRow->permissions = $loanPermissions;
                    $loanRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'loan';
                    $permission->permissions = $loanPermissions;
                    $permission->save();
                }
                // dd(DB::getQueryLog());
            } else {
                $loanRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'loan']])->first();

                $loanRow->permissions = $loanPermissions;
                $loanRow->save();
            }

            // Insurance module
            if ($request->input('insurance')!=NULL) {
                $insuranceRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'insurance']])->count();
                
                if ($insuranceRowCount > 0) {
                    $insuranceRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'insurance']])->first();

                    $insuranceRow->permissions = $loanPermissions;
                    $insuranceRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'insurance';
                    $permission->permissions = $loanPermissions;
                    $permission->save();
                }
            } else {
                $insuranceRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'insurance']])->first();

                $insuranceRow->permissions = $loanPermissions;
                $insuranceRow->save();
            }

            // creditCard module
            if ($request->input('creditCard')!=NULL) {
                $creditCardRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'creditCard']])->count();
                
                if ($creditCardRowCount > 0) {
                    $creditCardRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'creditCard']])->first();

                    $creditCardRow->permissions = $creditCardPermissions;
                    $creditCardRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'creditCard';
                    $permission->permissions = $creditCardPermissions;
                    $permission->save();
                }
            } else {
                $creditCardRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'creditCard']])->first();

                $creditCardRow->permissions = $creditCardPermissions;
                $creditCardRow->save();
            }

            // socialCard module
            if ($request->input('socialCard')!=NULL) {
                $socialCardRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'socialCard']])->count();
                
                if ($socialCardRowCount > 0) {
                    $socialCardRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'socialCard']])->first();

                    $socialCardRow->permissions = $socialCardPermissions;
                    $socialCardRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'socialCard';
                    $permission->permissions = $socialCardPermissions;
                    $permission->save();
                }
            } else {
                $socialCardRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'socialCard']])->first();

                $socialCardRow->permissions = $socialCardPermissions;
                $socialCardRow->save();
            }

            // socialBanner module
            if ($request->input('socialBanner')!=NULL) {
                $socialBannerRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'socialBanner']])->count();
                
                if ($socialBannerRowCount > 0) {
                    $socialBannerRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'socialBanner']])->first();

                    $socialBannerRow->permissions = $socialBannerPermissions;
                    $socialBannerRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'socialBanner';
                    $permission->permissions = $socialBannerPermissions;
                    $permission->save();
                }
            } else {
                $socialBannerRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'socialBanner']])->first();

                $socialBannerRow->permissions = $socialBannerPermissions;
                $socialBannerRow->save();
            }

            // notification module
            if ($request->input('notification')!=NULL) {
                $notificationRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'notification']])->count();
                
                if ($notificationRowCount > 0) {
                    $notificationRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'notification']])->first();

                    $notificationRow->permissions = $notificationPermissions;
                    $notificationRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'notification';
                    $permission->permissions = $notificationPermissions;
                    $permission->save();
                }
            } else {
                $notificationRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'notification']])->first();

                $notificationRow->permissions = $notificationPermissions;
                $notificationRow->save();
            }

            // payout module
            if ($request->input('payout')!=NULL) {
                $payoutRowCount = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'payout']])->count();
                
                if ($payoutRowCount > 0) {
                    $payoutRow = Permission::where([
                        ['user_type', '=' , $userType],
                        ['module', '=' , 'payout']])->first();

                    $payoutRow->permissions = $payoutPermissions;
                    $payoutRow->save();
                } else {
                    $permission = new Permission;
                    $permission->user_type = $userType;
                    $permission->module = 'payout';
                    $permission->permissions = $payoutPermissions;
                    $permission->save();
                }
            } else {
                $payoutRow = Permission::where([
                    ['user_type', '=' , $userType],
                    ['module', '=' , 'payout']])->first();

                $payoutRow->permissions = $payoutPermissions;
                $payoutRow->save();
            }
        } else {
        // If it doesn't have entries.

            // Admin Module
            if (!empty($request->input('admin'))) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'admin';
                $permission->permissions = $adminPermissions;
                $permission->save();
            }

            // Advisor module
            if ($request->input('advisor')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'advisor';
                $permission->permissions = $advisorPermissions;
                $permission->save();
            }

            // Loan module
            if ($request->input('loan')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'loan';
                $permission->permissions = $loanPermissions;
                $permission->save();
            }

            // Insurance module
            if ($request->input('insurance')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'insurance';
                $permission->permissions = $loanPermissions;
                $permission->save();
            }

            // creditCard module
            if ($request->input('creditCard')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'creditCard';
                $permission->permissions = $creditCardPermissions;
                $permission->save();
            }

            // socialCard module
            if ($request->input('socialCard')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'socialCard';
                $permission->permissions = $socialCardPermissions;
                $permission->save();
            }

            // socialBanner module
            if ($request->input('socialBanner')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'socialBanner';
                $permission->permissions = $socialBannerPermissions;
                $permission->save();
            }

            // notification module
            if ($request->input('notification')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'notification';
                $permission->permissions = $notificationPermissions;
                $permission->save();
            }

            // payout module
            if ($request->input('payout')!=NULL) {
                $permission = new Permission;
                $permission->user_type = $userType;
                $permission->module = 'payout';
                $permission->permissions = $payoutPermissions;
                $permission->save();
            }
        }

        $userTypes = user_type::where('id', $userType)->select('title')->first();
        $user_type = ($userTypes->title) ? $userTypes->title : "" ;

        return redirect()->route('crm-permission')->with('success',"Permission assigned successfully to '$user_type' !!");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        //
    }

    public function loadModules(Request $request)
    {
        $count = Permission::where('user_type',$_POST['userType'])->count();
        if ($count > 0) {
            $permissions = Permission::where('user_type',$_POST['userType'])->get()->pluck('permissions','module')->toArray();
            return view('crm.ajax.modules',compact('permissions'));
        } else {
            return view('crm.ajax.defaultModules');
        }
    }

}
