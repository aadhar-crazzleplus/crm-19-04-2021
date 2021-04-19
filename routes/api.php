<?php

use App\Http\Controllers\api\BusinessLoan;
use App\Http\Controllers\api\CreditCard;
use App\Http\Controllers\api\MyLead;
use App\Http\Controllers\api\PersonalLoan;
use App\Http\Controllers\api\TermInsurance;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\VehicleLoan;
use App\Http\Controllers\api\VehicleInsurance;
use App\Http\Controllers\api\PrCardController;
use App\Http\Controllers\api\PrBannerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware(['signature'])->group(function () {
    Route::get('dddd', function () {
        echo "ssssss";
    });

    // Route::post('besath_store_mpinb', [UserController::class, 'storeMpin']);
    Route::post('bulk_sms', [UserController::class, 'bulk_sms']);

    Route::post('besath_loginb', [UserController::class, 'login'])->name('login');
    Route::post('besath_true_loginb', [UserController::class, 'true_login']);
    Route::post('besath_banktestb', [UserController::class, 'bank']);
    Route::post('besath_checkupdateb', [UserController::class, 'checkupdate']);
    Route::post('besath_fcm_toknb', [UserController::class, 'fcmtokenpush']);
    // Route::post('get_veh_api', [VehicleInsurance::class, 'get_veh_api']);

    Route::middleware('auth:sanctum')->group( function () {
        // Advisor Profile/registration
        Route::get('besath_getuserb', [UserController::class, 'getuser']);
        Route::post('besath_update_accountb', [UserController::class, 'update_account']);
        Route::post('besath_getpincodeb', [UserController::class, 'getpincode']);
        Route::get('besath_getcityb', [UserController::class, 'getcity']);
        Route::get('besath_getcitystateb', [UserController::class, 'getcitystate']);
        Route::get('besath_getdropdownsb', [UserController::class, 'getDropDowns']);
        Route::post('besath_personal_detailsb', [UserController::class, 'personal_details']);
        Route::post('besath_bank_detailsb', [UserController::class, 'bank_details']);
        Route::post('besath_edu_detailsb', [UserController::class, 'edu_details']);
        Route::post('besath_pro_detailsb', [UserController::class, 'pro_details']);
        Route::post('besath_kyc_detailsb', [UserController::class, 'kyc_details']);
        Route::post('besath_profile_picb', [UserController::class, 'profile_pic']);
        Route::get('besath_myteamb', [UserController::class, 'myteam']);

        // Personal Loan lead
        Route::post('besath_getotpb', [PersonalLoan::class, 'getotp']);
        Route::post('besath_verify_otpb', [PersonalLoan::class, 'verify_otp']);
        Route::post('besath_addpl_profileb', [PersonalLoan::class, 'addpl_profile']);
        Route::post('besath_addpl_is_loanb', [PersonalLoan::class, 'addpl_is_loan']);
        Route::post('besath_addpl_incomeb', [PersonalLoan::class, 'addpl_income']);
        Route::get('besath_getproductsb', [PersonalLoan::class, 'getproducts']);
        Route::get('besath_company_listb', [PersonalLoan::class, 'company_list']);
        Route::post('besath_search_companiesb', [PersonalLoan::class, 'search_companies']);

        // Business Lead
        Route::post('besath_verify_otp_blb', [BusinessLoan::class, 'verify_otp']);
        Route::post('besath_addbl_profileb', [BusinessLoan::class, 'addbl_profile']);
        Route::post('besath_addbl_is_loanb', [BusinessLoan::class, 'addbl_is_loan']);
        Route::post('besath_addbl_incomeb', [BusinessLoan::class, 'addbl_income']);

        // Credit Card Lead
        Route::post('besath_verify_otp_cardb', [CreditCard::class, 'verify_otp']);
        Route::post('besath_addcard_profileb', [CreditCard::class, 'addcard_profile']);
        Route::post('besath_addcard_is_cardb', [CreditCard::class, 'addcard_is_card']);
        Route::post('besath_addcard_incomeb', [CreditCard::class, 'addcard_income']);
        Route::get('besath_get_occupationsb', [CreditCard::class, 'getoccupations']);
        Route::post('besath_card_deleteb', [CreditCard::class, 'destroy']);

        // Vehicle Loan Lead
        Route::post('besath_verify_otp_vehb', [VehicleLoan::class, 'verify_otp']);
        Route::post('besath_veh_loan_regb', [VehicleLoan::class, 'veh_loan_reg']);
        Route::post('besath_veh_loan_detailsb', [VehicleLoan::class, 'veh_loan_details']);
        Route::post('besath_addveh_profileb', [VehicleLoan::class, 'addvehicle_profile']);
        // Route::post('addveh_details', [VehicleLoan::class, 'addvehicle_detais']);
        Route::post('besath_addveh_uploadb', [VehicleLoan::class, 'addvehicle_uploads']);
        Route::post('besath_addveh_empb', [VehicleLoan::class, 'addvehicle_emp']);

        // MyLead
        Route::post('besath_myleadsb', [MyLead::class, 'myleads']);

        // Vehicle Insurance Lead
        Route::post('besath_verify_otp_veh_insb', [VehicleInsurance::class, 'verify_otp']);
        Route::post('besath_veh_regb', [VehicleInsurance::class, 'veh_reg']);
        Route::post('besath_veh_detailsb', [VehicleInsurance::class, 'veh_details']);
        Route::post('besath_veh_profileb', [VehicleInsurance::class, 'veh_profile']);

        // Term Insurance Lead
        Route::post('besath_verify_otp_term_insb', [TermInsurance::class, 'verify_otp']);
        Route::post('besath_term_profileb', [TermInsurance::class, 'term_profile']);

        // Pr Cards api
        Route::get('besath_social_cardb',[SocialCardController::class, 'getPrCards']);

        // Pr Banner api
        Route::get('besath_pr_bannerb',[PrBannerController::class, 'getPrBanners']);

        Route::post('besath_store_mpinb', [UserController::class, 'storeMpin']);
        Route::post('besath_verify_mpinb', [UserController::class, 'verifyMpin']);

        Route::put('besath_changemobile', [UserController::class, 'changeMobile']);

        // wallet history
        Route::get('besath_wallet_history',[UserController::class, 'walletHistory']);

        // User transection 
        Route::post('besath_user_transaction', [UserController::class, 'userTransaction']);
    });

});
