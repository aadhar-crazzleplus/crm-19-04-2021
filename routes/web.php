<?php

use App\Http\Controllers\crm\AdminController;
use App\Http\Controllers\crm\CommonController;
use App\Http\Controllers\crm\LoginController;
use App\Http\Controllers\crm\UserController;
use App\Http\Controllers\crm\PrBannerController;
use App\Http\Controllers\crm\PrCardImageController;
use App\Http\Controllers\crm\NotificationController;
use App\Http\Controllers\crm\PayoutController;
use App\Http\Controllers\crm\PermissionController;
use App\Http\Controllers\crm\DatabaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Permission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::get('/appdownload', function () {
    return redirect()->away('https://play.google.com/store/apps/details?id=com.app.banksathi');
});

// Route::any('/', function () {
//     return view('crm.login');
// });

Route::get('/', function () {
    // echo Hash::make("123456");
    return view('crm.login');
});

Route::get('/login', function () {
    return view('crm.login');
});

Route::get('/logout', function (Request $request) {
    Auth::logout();
    Auth::guard('admin')->logout();
    Artisan::call('cache:clear');
    return redirect("/")->with("status","You have been logged out!");
})->name('crm.logout');;

// Redirect default route service provider route to another route.
Route::redirect('/home', '/dashboard');

// Route::post('/login', [LoginController::class, "crmLogin"])->name('crm.login');

Route::post('/login', [LoginController::class, "crmLogin"])->name('crm.login');

// Route::get('/logout', [LoginController::class, "crmLogout"])->name('crm.logout');

// Route::get('/', [LoginController::class, "logincheck"])->middleware('auth');
Route::middleware(['crmauthcheck','web'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $sessionArray = Session::all();
        
        $user = Auth::guard('admin')->user();
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
        
        // echo "<pre>"; print_r($user); echo "</pre>"; die('  ');
        return view('crm.dashboard');
    })->name('crm.dashboard');

    // Admin Users
    Route::get('/add-admin', [AdminController::class, 'create'])->name('add-admin');
    Route::get('/edit-admin/{id}', [AdminController::class, 'edit'])->name('edit-admin');
    Route::post('/store-admin', [AdminController::class, 'store'])->name('store-admin');
    Route::post('/update-admin/{id}', [AdminController::class, 'update'])->name('update-admin');
    Route::get('/delete-admin/{id}', [AdminController::class, 'destroy'])->name('delete-admin');
    Route::get('/adminchangepass', [AdminController::class, 'change_password'])->name('admin-change-pass');
    Route::post('/adminchangepass', [AdminController::class, 'change_pass'])->name('admin-changepass');
    Route::get('/admins', [AdminController::class, 'index'])->name('admins');
    Route::get('/deleted-admin', [AdminController::class, 'deleted'])->name('deleted-admin');
    Route::get('/getadmins', [AdminController::class, 'getUser'])->name('getadmins');
    Route::get('/getdeleted-admin', [AdminController::class, 'getDeleted'])->name('getdeleted-admin');

    // Advisor users
    Route::get('/add-user', [UserController::class, 'create'])->name('crm.add-user');
    Route::get('/edit-user/{id}', [UserController::class, 'edit'])->name('crm.edit-user');
    Route::post('/store-user', [UserController::class, 'store'])->name('crm.store-user');
    Route::post('/update-user/{id}', [UserController::class, 'update'])->name('crm.update-user');
    Route::get('/delete-user/{id}', [UserController::class, 'destroy'])->name('crm.delete-user');
    Route::get('/changepass', [UserController::class, 'change_password'])->name('crm.change-pass');
    Route::post('/changepass', [UserController::class, 'change_pass'])->name('crm.changepass');
    Route::get('/users', [UserController::class, 'index'])->name('crm.users');
    Route::get('/deleted', [UserController::class, 'deleted'])->name('crm.deleted');
    Route::get('/getusers', [UserController::class, 'getUser'])->name('crm.getusers');
    Route::get('/getdeleted', [UserController::class, 'getDeleted'])->name('crm.getdeleted');

    // Common
    Route::get('/getpincode', [CommonController::class, 'getpincode']);
    Route::post('/getpincode', [CommonController::class, 'getpincode'])->name('crm.getpincode');
    Route::post('/getcity', [CommonController::class, 'getcity'])->name('crm.getcity');
    Route::post('/getcitystate', [CommonController::class, 'getcitystate'])->name('crm.getcitystate');

    // Leads
    Route::get('/loan-pl', [UserController::class, 'create'])->name('loan-pl');
    Route::get('/loan-bl', [UserController::class, 'edit'])->name('loan-bl');
    Route::post('/loan-uv', [UserController::class, 'store'])->name('loan-uv');
    Route::post('/vehicle-ins', [UserController::class, 'update'])->name('vehicle-ins');
    Route::get('/health-ins', [UserController::class, 'destroy'])->name('health-ins');
    Route::get('/life-ins', [UserController::class, 'change_password'])->name('life-ins');
    Route::post('/term-ins', [UserController::class, 'change_pass'])->name('term-ins');
    Route::get('/covid-ins', [UserController::class, 'index'])->name('covid-ins');
    Route::get('/credit-card', [UserController::class, 'index'])->name('credit-card');

    // Promotional card
    Route::get('/pr-card', [PrCardImageController::class, 'create'])->name('pr-card');
    Route::post('/store-pr-card', [PrCardImageController::class, 'store'])->name('crm.store-pr-cards');
    Route::get('/crm-pr-cards', [PrCardImageController::class, 'index'])->name('crm-pr-cards');
    Route::get('/getcards', [PrCardImageController::class, 'getCards'])->name('crm.getcards');
    Route::get('/delete-card/{id}', [PrCardImageController::class, 'destroy'])->name('crm.delete-card');

    // Banner Routes
    Route::get('/pr-banner', [PrBannerController::class, 'create'])->name('pr-banner');
    Route::post('/store-pr-banner', [PrBannerController::class, 'store'])->name('crm.store-pr-banners');
    Route::get('/crm-pr-banner', [PrBannerController::class, 'index'])->name('crm-pr-banners');
    Route::get('/getbanners', [PrBannerController::class, 'getCards'])->name('crm.getbanners');
    Route::get('/delete-banner/{id}', [PrBannerController::class, 'destroy'])->name('crm.delete-banner');

    // Notification Routes
    Route::get('/notification', [NotificationController::class, 'create'])->name('notification');
    Route::post('/store-notification', [NotificationController::class, 'store'])->name('crm.store-notification');
    Route::get('/crm-notification', [NotificationController::class, 'index'])->name('crm-notification');
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('crm.getnotifications');
    Route::get('/delete-notification/{id}', [NotificationController::class, 'destroy'])->name('crm.delete-banner');

    // Payout Routes
    Route::any('/payout', [PayoutController::class, 'create'])->name('payout');
    Route::post('/store-payout', [PayoutController::class, 'store'])->name('crm.store-payout');
    Route::get('/crm-payout', [PayoutController::class, 'index'])->name('crm-payout');
    Route::get('/getpayout', [PayoutController::class, 'getPayout'])->name('crm.getpayout');
    
    Route::get('/crm-import-payout', [PayoutController::class, 'importPayout'])->name('crm.importpayout');
    Route::get('/crm-compare-sheets', [PayoutController::class, 'compareSheets'])->name('crm.comparesheets');
    Route::post('/crm-compare-sheets', [PayoutController::class, 'sheetResults'])->name('crm.sheetResults');
    Route::post('/store-payout-file', [PayoutController::class, 'storePayoutFile'])->name('crm.store-payout-file');
    
    // crm.store-payout-file

    Route::get('/delete-payout/{id}', [PayoutController::class, 'destroy'])->name('crm.delete-payout');

    Route::any('/crm-permission', [PermissionController::class, 'create'])->name('crm-permission');
    Route::post('/store-permission', [PermissionController::class, 'store'])->name('crm.store-permission');
    Route::post('/load-modules', [PermissionController::class, 'loadModules'])->name('crm.load-modules');
    
    Route::any('/crm-database', [DatabaseController::class, 'index'])->name('crm-database');
    Route::any('/crm-comparedatabases', [DatabaseController::class, 'showResults'])->name('crm.comparedatabases');

});

Route::post('/sample-file-download', [PayoutController::class, 'sampleFileDownload'])->name('crm.sampleFileDownload');