<?php

namespace App\Http\Controllers\crm;
require base_path(). '/vendor/autoload.php';

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\Wallet;
use App\Models\User;
use App\Models\leads;
use App\Models\city;
use App\Models\lead_addresses;
use App\Models\lead_is_cards;
use App\Models\lead_is_loans;
use App\Models\lead_profiles;
use App\Models\lead_vehicles;
use App\Models\UsedVehicleMis;
use App\Models\CreditCardRequest;
use App\Models\InsuranceMis;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class PayoutController extends Controller
{

    public $headers = array();
    public $insurance = array();

    public function __construct(Request $request) {
        // $sessionArray = Session::get('sessionArray');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('crm.payouts.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('crm.payouts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'insurance' => 'required',
            'payout_file' => 'required|max:2048',
        ]);

        if ($validator->fails()) {
            echo "Validation fails";
        } else {
            $fileNameToStore='';
            $payout_file = "";
            $year = date("Y");
            $month = date("M");
            $path = 'public/payouts/';
    
            $publicPath = 'payouts/';
            if($request->hasFile('payout_file')){
                // file upload
                $file = $request->file('payout_file');
                // Get filename with extension.
                $fileNameWithExt = $file->getClientOriginalName();
    
                // Get just the filename.
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
    
                // Sanitizing Filename.
                $filename = filter_var($filename, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                $filename = $this->sanitizeString($filename);
    
                // Get the file extension.
                $extension = $file->getClientOriginalExtension();
                // Create new filename.
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
    
                $payout_file = time().$payout_file;
                $request->file('payout_file')->storeAs($path, $fileNameToStore);
    
                // Insert Banner Details
                $payout = new Payout;
                $payout->title = $request->input('title');
                $payout->file = $publicPath.$fileNameToStore;
                $payout->save();
            }
        }
                        
        return redirect()->route('crm.store-payout')->with('success', 'Payout created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payout $payout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }


    public function getPayout(Request $request)
    {
        // die('getPayout');
    }

    // importPayout
    public function importPayout(Request $request)
    {
        return view('crm.payouts.importPayout');
    }

    // storePayoutFile
    public function storePayoutFile(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'file_type' => 'required|integer',
            'payout_file' => 'required|max:10000|mimes:xls,xlsx',
        ]);

        $file_type = $request->input('file_type');
        $file = $request->file('payout_file');

        if (!$request->file('payout_file')) {
            return redirect()->route('crm.importpayout')->with('error', "Opps! Please provide a file to import...");
        }

        try {
            /** Load $inputFileName to a Spreadsheet Object  **/
            // $inputFileName = storage_path('app/public/payouts/Used_Vehicle_MIS.xlsx');

            if ($file_type==1) {
                //For Used Vehicle MIS
                $inputFileName = $file->getRealPath();
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                $sheetCount = $spreadsheet->getSheetCount();
                $sheetNames = $spreadsheet->getSheetNames();

                if($sheetCount > 0)
                {
                    if (in_array("Used Vehicle", $sheetNames))
                    {
                        $worksheet = $spreadsheet->getSheetByName('Used Vehicle');
                        $insertFlag = 1;
                        $usedVehicleArr = [];
                        foreach ($worksheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                                            //    even if a cell value is not set.
                                                                            // For 'TRUE', we loop through cells
                                                                            //    only when their value is set.
                                                                            // If this method is not called,
                                                                            //    the default value is 'false'.
                            
                            foreach ($cellIterator as $cell) {
                                if ($cell->getRow()==1) {
                                    // echo '<th>';
                                    if (trim($cell->getValue())!="") {
                                        // echo " -> ". $cell->getValue();
                                        $usedVehicleArr[] = Str::slug($cell->getValue(), '_');
                                        // if (in_array(Str::slug($cell->getValue(), '_'), $usedVehicleArr))
                                        // { 
                                        //     echo Str::slug($cell->getValue(), '_');
                                        //     die;
                                        // }
                                    }
                                    // echo '</th>';
                                    // if(trim($cell->getValue()) == "") {
                                    //     return redirect()->route('crm.importpayout')->with('error', 'Opps! Please provide sheet in the given format..Download it from "Sample Excels".');
                                    // }
                                } else {

                                    if(($cell->getColumn()=="A") && ($cell->getRow()!=1)) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $leadDate = date("Y-m-d", $date);
                                        } else {
                                            $leadDate = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="I") && ($cell->getRow()!=1)) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $calling_date = date("Y-m-d", $date);
                                        } else {
                                            $calling_date = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="Q") && ($cell->getRow()!=1)) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $follow_up_date_1 = date("Y-m-d", $date);
                                        } else {
                                            $follow_up_date_1 = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="R") && ($cell->getRow()!=1)) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $follow_up_date_2 = date("Y-m-d", $date);
                                        } else {
                                            $follow_up_date_2 = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="S") && ($cell->getRow()!=1)) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $follow_up_date_3 = date("Y-m-d", $date);
                                        } else {
                                            $follow_up_date_3 = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="T") && ($cell->getRow()!=1)) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $follow_up_date_4 = date("Y-m-d", $date);
                                        } else {
                                            $follow_up_date_4 = NULL;
                                        }
                                    } elseif($cell->getColumn()=="B") {
                                        if($cell->getValue()!=NULL) {
                                            $source = $cell->getValue();
                                        } else {
                                            $source = NULL;
                                        }
                                    } elseif($cell->getColumn()=="C") {
                                        if($cell->getValue()!=NULL) {
                                            $advisor_name = $cell->getValue();
                                        } else {
                                            $advisor_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="D") {
                                        if($cell->getValue()!=NULL) {
                                            $email = $cell->getValue();
                                        } else {
                                            $email = NULL;
                                        }
                                    } elseif($cell->getColumn()=="E") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_name = $cell->getValue();
                                        } else {
                                            $customer_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="F") {
                                        if($cell->getValue()!=NULL) {
                                            $phone_no = $cell->getValue();
                                        } else {
                                            $phone_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="G") {
                                        if($cell->getValue()!=NULL) {
                                            $alt_no = $cell->getValue();
                                        } else {
                                            $alt_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="H") {
                                        if($cell->getValue()!=NULL) {
                                            $location = $cell->getValue();
                                        } else {
                                            $location = NULL;
                                        }
                                    } elseif($cell->getColumn()=="J") {
                                        if($cell->getValue()!=NULL) {
                                            $vehicle_name = $cell->getValue();
                                        } else {
                                            $vehicle_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="K") {
                                        if($cell->getValue()!=NULL) {
                                            $vehicle_segment = $cell->getValue();
                                        } else {
                                            $vehicle_segment = NULL;
                                        }
                                    } elseif($cell->getColumn()=="L") {
                                        if($cell->getValue()!=NULL) {
                                            $vehicle_model = $cell->getValue();
                                        } else {
                                            $vehicle_model = NULL;
                                        }
                                    } elseif($cell->getColumn()=="M") {
                                        if($cell->getValue()!=NULL) {
                                            $loan_type = $cell->getValue();
                                        } else {
                                            $loan_type = NULL;
                                        }
                                    } elseif($cell->getColumn()=="N") {
                                        if($cell->getValue()!=NULL) {
                                            $lead_status = $cell->getValue();
                                        } else {
                                            $lead_status = NULL;
                                        }
                                    } elseif($cell->getColumn()=="O") {
                                        if($cell->getValue()!=NULL) {
                                            $prospect_month = $cell->getValue();
                                        } else {
                                            $prospect_month = NULL;
                                        }
                                    } elseif($cell->getColumn()=="P") {
                                        if($cell->getValue()!=NULL) {
                                            $prospect_amount = $cell->getValue();
                                        } else {
                                            $prospect_amount = NULL;
                                        }
                                    } elseif($cell->getColumn()=="U") {
                                        if($cell->getValue()!=NULL) {
                                            $final_remark = $cell->getValue();
                                        } else {
                                            $final_remark = NULL;
                                        }
                                    } elseif($cell->getColumn()=="V") {
                                        if($cell->getValue()!=NULL) {
                                            $final_status = $cell->getValue();
                                        } else {
                                            $final_status = NULL;
                                        }
                                    } elseif($cell->getColumn()=="W") {
                                        if($cell->getValue()!=NULL) {
                                            $disbursement_date = $cell->getValue();
                                        } else {
                                            $disbursement_date = NULL;
                                        }
                                    } elseif($cell->getColumn()=="X") {
                                        if($cell->getValue()!=NULL) {
                                            $loan_amount = $cell->getValue();
                                        } else {
                                            $loan_amount = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Y") {
                                        if($cell->getValue()!=NULL) {
                                            $roi = $cell->getValue();
                                        } else {
                                            $roi = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Z") {
                                        if($cell->getValue()!=NULL) {
                                            $amount_recvd = $cell->getValue();
                                        } else {
                                            $amount_recvd = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AA") {
                                        if($cell->getValue()!=NULL) {
                                            $payout_to_advisor = $cell->getValue();
                                        } else {
                                            $payout_to_advisor = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AB") {
                                        if($cell->getValue()!=NULL) {
                                            $payment_recvd_month = $cell->getValue();
                                        } else {
                                            $payment_recvd_month = NULL;
                                        }
                                    }
                                }
                            }
                            

                            if($insertFlag!=1) {
                                if ($source=='Advisor') {
                                    // Demo location
                                    $location = "jaipur";
                                    if ($location!=NULL) {
                                        $cityData = city::where('city_name', 'like', "%$location%")->first()->toArray();
                                    } else {
                                        $cityData=[];
                                    }

                                    // Demo Advisor
                                    $advisor_name = 'Firoz';
                                    $user = User::where('first_name', 'like', "%$advisor_name%")->first('id')->toArray();
                                    $advisor_id = ($user['id']) ? $user['id'] : "";

                                    // lead Status
                                    $incomplete = array("Not Connected","Follow Up","Logged-in");
                                    $processing = array("Under Process","Pending for approval","Approved","Logged-in","Rejected","Not Eligible","Not Interested","Other Product Inquiry","Prospect","Disbursed","Other Product Inquiry","Prospect");
                                    $closed = array("Approved","Not Eligible","Not Interested",);
                                    $rejected = array("Rejected");

                                    if (in_array($lead_status, $incomplete)) {
                                        $leadStatus = "i";
                                    } elseif (in_array($lead_status, $processing)) {
                                        $leadStatus = "p";
                                    } elseif (in_array($lead_status, $closed)) {
                                        $leadStatus = "c";
                                    } elseif (in_array($lead_status, $rejected)) {
                                        $leadStatus = "r";
                                    } else {
                                        $leadStatus = "i";
                                    }

                                    $lead_profiles = new lead_profiles;
                                    $lead_profiles->email = $email;
                                    $lead_profiles->full_name = $customer_name;
                                    $lead_profiles->mobile_no = $phone_no;
                                    $lead_profiles->save();
                                    $str = "LEADPRFLE";
                                    $lead_profilesId = str_pad($str,15,0);
                                    $lead_profiles->unique_id = $lead_profilesId.$lead_profiles->id;
                                    $lead_profiles->save();
                                    

                                    $lead_addresses = new lead_addresses;
                                    $lead_addresses->lead_profile_id = $lead_profiles->id;
                                    $lead_addresses->city_id = $cityData['id'];
                                    $lead_addresses->state_id = $cityData['state_id'];
                                    $lead_addresses->save();
                                    $str = "LDADSS".$cityData['id'].$cityData['state_id'];
                                    $lead_addressesId = str_pad($str,15,0);
                                    $lead_addresses->unique_id = $lead_addressesId.$lead_addresses->id;
                                    $lead_addresses->save();


                                    $vehicle_segment = $this->uniqueString($vehicle_segment);
                                    if ($vehicle_segment=="personal") {
                                        $ustrg = 'PRSL';
                                    } elseif ($vehicle_segment=="taxi") {
                                        $ustrg = 'TAXI';
                                    } elseif ($vehicle_segment=="commercial") {
                                        $ustrg = 'CMCL';
                                    } elseif ($vehicle_segment=="heavyvehicle") {
                                        $ustrg = 'HYVE';
                                    } elseif ($vehicle_segment=="agriculture") {
                                        $ustrg = 'AGRE';
                                    }

                                    $leads = new leads;
                                    $leads->lead_by = $advisor_id;
                                    $leads->assign_to = $advisor_id;
                                    $leads->lead_profile_id = $lead_profiles->id;
                                    $leads->lead_status = $leadStatus;
                                    $leads->lead_remark = $final_remark;
                                    $leads->save();
                                    $str = "LD".$ustrg;
                                    $leadsId = str_pad($str,15,0);
                                    $leads->unique_id = $leadsId.$leads->id;
                                    $leads->save();


                                    $wallet = new Wallet;
                                    $wallet->lead_id = $leads->id;
                                    $wallet->user_id = $advisor_id;
                                    $wallet->loan_type = $loan_type;
                                    $wallet->prospect_month = $prospect_month;
                                    $wallet->prospect_amount = $prospect_amount;
                                    $wallet->disbursement_date = $disbursement_date;
                                    $wallet->amount_recvd = $amount_recvd;
                                    $wallet->roi = $roi;
                                    $wallet->payout_to_advisor = $payout_to_advisor;
                                    $wallet->save();
                                    $str = "WLLT";
                                    $walletId = str_pad($str,8,0);
                                    $wallet->unique_id = $walletId.$wallet->id;
                                    $wallet->save();

                                }
                            }

                            /* if($insertFlag!=1) {
                                if(($leadDate==NULL) && ($calling_date==NULL) && ($follow_up_date_1==NULL) && ($follow_up_date_2==NULL) && ($follow_up_date_3==NULL) && ($follow_up_date_4==NULL) && ($source==NULL) && ($advisor_name==NULL) && ($email==NULL) && ($customer_name==NULL) && ($phone_no==NULL) && ($alt_no==NULL) && ($location==NULL) && ($vehicle_name==NULL) && ($vehicle_segment==NULL) && ($vehicle_model==NULL) && ($loan_type==NULL) && ($lead_status==NULL) && ($prospect_month==NULL) && ($prospect_amount==NULL) && ($final_remark==NULL) && ($final_status==NULL) && ($disbursement_date==NULL) && ($loan_amount==NULL) && ($roi==NULL) && ($amount_recvd==NULL) && ($payout_to_advisor==NULL) && ($payment_recvd_month==NULL)) 
                                {
                                    continue;
                                }
                                $usedVehicleMis = new UsedVehicleMis;
                                $usedVehicleMis->lead_date = $leadDate;
                                $usedVehicleMis->source = $source;
                                $usedVehicleMis->advisor_name = $advisor_name;
                                $usedVehicleMis->email = $email;
                                $usedVehicleMis->customer_name = $customer_name;
                                $usedVehicleMis->phone_no = $phone_no;
                                $usedVehicleMis->alt_no = $alt_no;
                                $usedVehicleMis->location = $location;
                                $usedVehicleMis->calling_date = $calling_date;
                                $usedVehicleMis->vehicle_name = $vehicle_name;
                                $usedVehicleMis->vehicle_segment = $vehicle_segment;
                                $usedVehicleMis->vehicle_model = $vehicle_model;
                                $usedVehicleMis->loan_type = $loan_type;
                                $usedVehicleMis->lead_status = $lead_status;
                                $usedVehicleMis->prospect_month = $prospect_month;
                                $usedVehicleMis->prospect_amount = $prospect_amount;
                                $usedVehicleMis->follow_up_date_1 = $follow_up_date_1;
                                $usedVehicleMis->follow_up_date_2 = $follow_up_date_2;
                                $usedVehicleMis->follow_up_date_3 = $follow_up_date_3;
                                $usedVehicleMis->follow_up_date_4 = $follow_up_date_4;
                                $usedVehicleMis->final_remark = $final_remark;
                                $usedVehicleMis->final_status = $final_status;
                                $usedVehicleMis->disbursement_date = $disbursement_date;
                                $usedVehicleMis->loan_amount = $loan_amount;
                                $usedVehicleMis->roi = $roi;
                                $usedVehicleMis->amount_recvd = $amount_recvd;
                                $usedVehicleMis->payout_to_advisor = $payout_to_advisor;
                                $usedVehicleMis->payment_recvd_month = $payment_recvd_month;
                                $usedVehicleMis->status = $request->input('status');
                                $usedVehicleMis->save();
                            } */
                            $insertFlag++;
                        }


                        return redirect()->route('crm.importpayout')->with('success', 'Used Vehicle Loan Data imported successfully.');
                    } 
                    else 
                    {
                        return redirect()->route('crm.importpayout')->with('error', 'Opps! Specified Sheet (Used Vehicle) does not exists in the excel..');
                    }
                } 
                else
                {
                    return redirect()->route('crm.importpayout')->with('error', 'Sheet is empty.');
                }
            } elseif ($file_type==2) {
                // Credit card request (Responses)
                $inputFileName = $file->getRealPath();
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

                $sheetCount = $spreadsheet->getSheetCount();
                $sheetNames = $spreadsheet->getSheetNames();

                if($sheetCount > 0)
                {
                    if (in_array("Form Responses 1", $sheetNames))
                    {
                        $worksheet = $spreadsheet->getSheetByName('Form Responses 1');
                        // echo '<table>' . PHP_EOL;
                        $insertFlag = 1;
                        foreach ($worksheet->getRowIterator() as $row) {
                            // echo '<tr>' . PHP_EOL;
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                                            //    even if a cell value is not set.
                                                                            // For 'TRUE', we loop through cells
                                                                            //    only when their value is set.
                                                                            // If this method is not called,
                                                                            //    the default value is 'false'.
                        
                            foreach ($cellIterator as $cell) {
                                // var_dump($cell->getRow());
                                if ($cell->getRow()==1) {
                                    // echo '<th>';
                                    // if (trim($cell->getValue())!="") { echo $cell->getValue(); }
                                    // echo '</th>';
                                    if(trim($cell->getValue()) == "") {
                                        return redirect()->route('crm.importpayout')->with('error', 'Opps! Please provide sheet in the given format..Download it from "Sample Excels".');
                                    }
                                } else {

                                    // echo '<td>' .
                                    // $cell->getValue() .
                                    // '</td>' . PHP_EOL;

                                    if(($cell->getColumn()=="N")) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $date_of_birth = date("Y-m-d", $date);
                                        } else {
                                            $date_of_birth = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="A")) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $timestamp = date("Y-m-d", $date);
                                        } else {
                                            $timestamp = NULL;
                                        }
                                    } elseif($cell->getColumn()=="B") {
                                        if($cell->getValue()!=NULL) {
                                            $lead_owner_name = $cell->getValue();
                                        } else {
                                            $lead_owner_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="C") {
                                        if($cell->getValue()!=NULL) {
                                            $lead_owner_contact_no  = $cell->getValue();
                                        } else {
                                            $lead_owner_contact_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="D") {
                                        if($cell->getValue()!=NULL) {
                                            $lead_number = $cell->getValue();
                                        } else {
                                            $lead_number = NULL;
                                        }
                                    } elseif($cell->getColumn()=="E") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_name = $cell->getValue();
                                        } else {
                                            $customer_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="F") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_mobile_number  = $cell->getValue();
                                        } else {
                                            $customer_mobile_number = NULL;
                                        }
                                    } elseif($cell->getColumn()=="G") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_address = $cell->getValue();
                                        } else {
                                            $customer_address = NULL;
                                        }
                                    } elseif($cell->getColumn()=="H") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_pin_code = $cell->getValue();
                                        } else {
                                            $customer_pin_code = NULL;
                                        }
                                    } elseif($cell->getColumn()=="I") {
                                        if($cell->getValue()!=NULL) {
                                            $required_credit_card = $cell->getValue();
                                        } else {
                                            $required_credit_card = NULL;
                                        }
                                    } elseif($cell->getColumn()=="J") {
                                        if($cell->getValue()!=NULL) {
                                            $base = $cell->getValue();
                                        } else {
                                            $base = NULL;
                                        }
                                    } elseif($cell->getColumn()=="K") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_email_id = $cell->getValue();
                                        } else {
                                            $customer_email_id = NULL;
                                        }
                                    } elseif($cell->getColumn()=="L") {
                                        if($cell->getValue()!=NULL) {
                                            $existing_card_card_bank = $cell->getValue();
                                        } else {
                                            $existing_card_card_bank = NULL;
                                        }
                                    } elseif($cell->getColumn()=="M") {
                                        if($cell->getValue()!=NULL) {
                                            $existing_card_max_limit = $cell->getValue();
                                        } else {
                                            $existing_card_max_limit = NULL;
                                        }
                                    } elseif($cell->getColumn()=="O") {
                                        if($cell->getValue()!=NULL) {
                                            $pan_card_no = $cell->getValue();
                                        } else {
                                            $pan_card_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="P") {
                                        if($cell->getValue()!=NULL) {
                                            $occupation  = $cell->getValue();
                                        } else {
                                            $occupation  = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Q") {
                                        if($cell->getValue()!=NULL) {
                                            $income = $cell->getValue();
                                        } else {
                                            $income = NULL;
                                        }
                                    } elseif($cell->getColumn()=="R") {
                                        if($cell->getValue()!=NULL) {
                                            $company_name = $cell->getValue();
                                        } else {
                                            $company_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="S") {
                                        if($cell->getValue()!=NULL) {
                                            $designation = $cell->getValue();
                                        } else {
                                            $designation = NULL;
                                        }
                                    } elseif($cell->getColumn()=="T") {
                                        if($cell->getValue()!=NULL) {
                                            $office_address = $cell->getValue();
                                        } else {
                                            $office_address  = NULL;
                                        }
                                    } elseif($cell->getColumn()=="U") {
                                        if($cell->getValue()!=NULL) {
                                            $office_pin_code = $cell->getValue();
                                        } else {
                                            $office_pin_code = NULL;
                                        }
                                    } elseif($cell->getColumn()=="V") {
                                        if($cell->getValue()!=NULL) {
                                            $status = $cell->getValue();
                                        } else {
                                            $status = NULL;
                                        }
                                    } elseif($cell->getColumn()=="W") {
                                        if($cell->getValue()!=NULL) {
                                            $product_reason = $cell->getValue();
                                        } else {
                                            $product_reason = NULL;
                                        }
                                    } elseif($cell->getColumn()=="X") {
                                        if($cell->getValue()!=NULL) {
                                            $product_type = $cell->getValue();
                                        } else {
                                            $product_type = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Y") {
                                        if($cell->getValue()!=NULL) {
                                            $form_no = $cell->getValue();
                                        } else {
                                            $form_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Z") {
                                        if($cell->getValue()!=NULL) {
                                            $lead_given = $cell->getValue();
                                        } else {
                                            $lead_given = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AA") {
                                        if($cell->getValue()!=NULL) {
                                            $lead_completed = $cell->getValue();
                                        } else {
                                            $lead_completed = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AB") {
                                        if($cell->getValue()!=NULL) {
                                            $picked_by = $cell->getValue();
                                        } else {
                                            $picked_by = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AC") {
                                        if($cell->getValue()!=NULL) {
                                            $payment_received_month = $cell->getValue();
                                        } else {
                                            $payment_received_month = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AD") {
                                        if($cell->getValue()!=NULL) {
                                            $paid_to_lg_lc = $cell->getValue();
                                        } else {
                                            $paid_to_lg_lc = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AE") {
                                        if($cell->getValue()!=NULL) {
                                            $dsa = $cell->getValue();
                                        } else {
                                            $dsa = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AF")) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $sourcing_date = date("Y-m-d", $date);
                                        } else {
                                            $sourcing_date = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AG")) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $dispatched_date = date("Y-m-d", $date);
                                        } else {
                                            $dispatched_date = NULL;
                                        }
                                    }
                                }
                            }
                            // echo '</tr>' . PHP_EOL;

                            if($insertFlag!=1) {

                                if(($date_of_birth==NULL) && ($timestamp==NULL) && ($lead_owner_name==NULL) && ($lead_owner_contact_no==NULL) && ($lead_number==NULL) && ($customer_name==NULL) && ($customer_mobile_number==NULL) && ($customer_address==NULL) && ($customer_pin_code==NULL) && ($required_credit_card==NULL) && ($base==NULL) && ($customer_email_id==NULL) && ($existing_card_card_bank==NULL) && ($existing_card_max_limit==NULL) && ($pan_card_no==NULL) && ($occupation==NULL) && ($income==NULL) && ($company_name==NULL) && ($designation==NULL) && ($office_address==NULL) && ($office_pin_code==NULL) && ($status==NULL) && ($product_reason==NULL) && ($product_type==NULL) && ($form_no==NULL) && ($lead_given==NULL) && ($lead_completed==NULL) && ($picked_by==NULL) && ($payment_received_month==NULL) && ($paid_to_lg_lc==NULL) && ($dsa==NULL) && ($sourcing_date==NULL) && ($dispatched_date==NULL)) 
                                {
                                    continue;
                                }

                                    $creditCardRequest = new CreditCardRequest;
                                    $creditCardRequest->date_of_birth = $date_of_birth;
                                    $creditCardRequest->timestamp = $timestamp;
                                    $creditCardRequest->lead_owner_name = $lead_owner_name;
                                    $creditCardRequest->lead_owner_contact_no = $lead_owner_contact_no;
                                    $creditCardRequest->lead_number = $lead_number;
                                    $creditCardRequest->customer_name = $customer_name;
                                    $creditCardRequest->customer_mobile_number = $customer_mobile_number;
                                    $creditCardRequest->customer_address = $customer_address;
                                    $creditCardRequest->customer_pin_code = $customer_pin_code;
                                    $creditCardRequest->required_credit_card = $required_credit_card;
                                    $creditCardRequest->base = $base;
                                    $creditCardRequest->customer_email_id = $customer_email_id;
                                    $creditCardRequest->existing_card_card_bank = $existing_card_card_bank;
                                    $creditCardRequest->existing_card_max_limit = $existing_card_max_limit;
                                    $creditCardRequest->pan_card_no = $pan_card_no;
                                    $creditCardRequest->income = $income;
                                    $creditCardRequest->company_name = $company_name;
                                    $creditCardRequest->designation = $designation;
                                    $creditCardRequest->office_address = $office_address;
                                    $creditCardRequest->office_pin_code = $office_pin_code;
                                    $creditCardRequest->status = $status;
                                    $creditCardRequest->product_reason = $product_reason;
                                    $creditCardRequest->product_type = $product_type;
                                    $creditCardRequest->form_no = $form_no;
                                    $creditCardRequest->lead_given = $lead_given;
                                    $creditCardRequest->lead_completed = $lead_completed;
                                    $creditCardRequest->picked_by = $picked_by;
                                    $creditCardRequest->payment_received_month = $payment_received_month;
                                    $creditCardRequest->paid_to_lg_lc = $paid_to_lg_lc;
                                    $creditCardRequest->dsa = $dsa;
                                    $creditCardRequest->sourcing_date = $sourcing_date;
                                    $creditCardRequest->dispatched_date = $dispatched_date;
                                    $creditCardRequest->save();
                            }
                            
                            $insertFlag++;
                        }
                        // echo '</table>' . PHP_EOL;
                        return redirect()->route('crm.importpayout')->with('success', 'Credit Card Request Data imported successfully.');
                    }
                    else
                    {
                        return redirect()->route('crm.importpayout')->with('error', 'Opps! Specified Sheet does not exists in the excel..');
                    }
                }
                else
                {
                    return redirect()->route('crm.importpayout')->with('error', 'Sheet is empty.');
                }
            } elseif ($file_type==3) {
                // Insurance Sheet MIS
                // Credit card request (Responses)
                $inputFileName = $file->getRealPath();
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

                $sheetCount = $spreadsheet->getSheetCount();
                $sheetNames = $spreadsheet->getSheetNames();

                if($sheetCount > 0)
                {
                    if (in_array("Sheet1", $sheetNames))
                    {
                        $worksheet = $spreadsheet->getSheetByName('Sheet1');
                        $worksheet->setCellValue('AF12', '=SUM(AF2:AF11)');
                        $totalAmount = $worksheet->getCell('AF12')->getCalculatedValue();
                        
                        // echo '<table>' . PHP_EOL;
                        $insertFlag = 1;
                        $insuranceArr = [];
                        foreach ($worksheet->getRowIterator() as $row) {
                            // echo '<tr>' . PHP_EOL;
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                                            //    even if a cell value is not set.
                                                                            // For 'TRUE', we loop through cells
                                                                            //    only when their value is set.
                                                                            // If this method is not called,
                                                                            //    the default value is 'false'.
                            $i=1;
                            // $insurance = array();
                            foreach ($cellIterator as $cell) {
                                // var_dump($cell->getRow());
                                if ($cell->getRow()==1) {
                                    // if(trim($cell->getValue()) == "") {
                                    //     // return redirect()->route('crm.importpayout')->with('error', 'Opps! Please provide sheet in the given format..Download it from "Sample Excels".');
                                    // }
                                    
                                    // echo '<th>';
                                    // if (trim($cell->getValue())!="") { echo $i. ' -> ' . $cell->getValue(); echo ' | '; }
                                    // echo '</th>';
                                    if (trim($cell->getValue())!="") { array_push($this->insurance,$cell->getValue()); }
                                } else {

                                    // echo '<td>' .
                                    // $cell->getValue() .
                                    // '</td>' . PHP_EOL;

                                    if(($cell->getColumn()=="A")) {
                                        if($cell->getValue()!=NULL) {
                                            $month = $cell->getValue();
                                        } else {
                                            $month = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="B")) {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $timestamp = date("Y-m-d", $date);
                                        } else {
                                            $timestamp = NULL;
                                        }
                                    } elseif($cell->getColumn()=="C") {
                                        if($cell->getValue()!=NULL) {
                                            $advisor_name = $cell->getValue();
                                        } else {
                                            $advisor_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="D") {
                                        if($cell->getValue()!=NULL) {
                                            $advisor_contact_no  = $cell->getValue();
                                        } else {
                                            $advisor_contact_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="E") {
                                        if($cell->getValue()!=NULL) {
                                            $vehicle_type = $cell->getValue();
                                        } else {
                                            $vehicle_type = NULL;
                                        }
                                    } elseif($cell->getColumn()=="F") {
                                        if($cell->getValue()!=NULL) {
                                            $vehicle_model = $cell->getValue();
                                        } else {
                                            $vehicle_model = NULL;
                                        }
                                    } elseif($cell->getColumn()=="G") {
                                        if($cell->getValue()!=NULL) {
                                            $variant  = $cell->getValue();
                                        } else {
                                            $variant = NULL;
                                        }
                                    } elseif($cell->getColumn()=="H") {
                                        if($cell->getValue()!=NULL) {
                                            $vehicle_rc = $cell->getValue();
                                        } else {
                                            $vehicle_rc = NULL;
                                        }
                                    } elseif($cell->getColumn()=="I") {
                                        if($cell->getValue()!=NULL) {
                                            $previous_insurance = $cell->getValue();
                                        } else {
                                            $previous_insurance = NULL;
                                        }
                                    } elseif($cell->getColumn()=="J") {
                                        if($cell->getValue()!=NULL) {
                                            $claim_in_previous_year = $cell->getValue();
                                        } else {
                                            $claim_in_previous_year = NULL;
                                        }
                                    } elseif($cell->getColumn()=="K") {
                                        if($cell->getValue()!=NULL) {
                                            $customer_name = $cell->getValue();
                                        } else {
                                            $customer_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="L") {
                                        if($cell->getValue()!=NULL) {
                                            $owner_contact_no = $cell->getValue();
                                        } else {
                                            $owner_contact_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="M") {
                                        if($cell->getValue()!=NULL) {
                                            $owner_mail_id = $cell->getValue();
                                        } else {
                                            $owner_mail_id = NULL;
                                        }
                                    } elseif($cell->getColumn()=="N") {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $owner_date_of_birth = date("Y-m-d", $date);
                                        } else {
                                            $owner_date_of_birth = NULL;
                                        }
                                    } elseif($cell->getColumn()=="O") {
                                        if($cell->getValue()!=NULL) {
                                            $nominee_name = $cell->getValue();
                                        } else {
                                            $nominee_name = NULL;
                                        }
                                    } elseif($cell->getColumn()=="P") {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $nominee_date_of_birth = date("Y-m-d", $date);
                                        } else {
                                            $nominee_date_of_birth = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Q") {
                                        if($cell->getValue()!=NULL) {
                                            $relation_with_nominee = $cell->getValue();
                                        } else {
                                            $relation_with_nominee = NULL;
                                        }
                                    } elseif($cell->getColumn()=="R") {
                                        if($cell->getValue()!=NULL) {
                                            $policy_type = $cell->getValue();
                                        } else {
                                            $policy_type = NULL;
                                        }
                                    } elseif($cell->getColumn()=="S") {
                                        if($cell->getValue()!=NULL) {
                                            $addon_required = $cell->getValue();
                                        } else {
                                            $addon_required = NULL;
                                        }
                                    } elseif($cell->getColumn()=="T") {
                                        if($cell->getValue()!=NULL) {
                                            $policy_status = $cell->getValue();
                                        } else {
                                            $policy_status  = NULL;
                                        }
                                    } elseif($cell->getColumn()=="U") {
                                        if($cell->getValue()!=NULL) {
                                            $policy_no = $cell->getValue();
                                        } else {
                                            $policy_no = NULL;
                                        }
                                    } elseif($cell->getColumn()=="V") {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $payment_date = date("Y-m-d", $date);
                                        } else {
                                            $payment_date = NULL;
                                        }
                                    } elseif($cell->getColumn()=="W") {
                                        if($cell->getValue()!=NULL) {
                                            $policy = $cell->getHyperlink()->getUrl();
                                        } else {
                                            $policy = NULL;
                                        }
                                    } elseif($cell->getColumn()=="X") {
                                        if($cell->getValue()!=NULL) {
                                            $value = (int) $cell->getValue();
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                                            $policy_expiry_date = date("Y-m-d", $date);
                                        } else {
                                            $policy_expiry_date = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Y") {
                                        if($cell->getValue()!=NULL) {
                                            $insurance_company = $cell->getValue();
                                        } else {
                                            $insurance_company = NULL;
                                        }
                                    } elseif($cell->getColumn()=="Z") {
                                        if($cell->getValue()!=NULL) {
                                            $premium_without_gst = $cell->getValue();
                                        } else {
                                            $premium_without_gst = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AA") {
                                        if($cell->getValue()!=NULL) {
                                            $gst = $cell->getValue();
                                        } else {
                                            $gst = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AB") {
                                        if($cell->getValue()!=NULL) {
                                            $total_premium = $cell->getValue();
                                        } else {
                                            $total_premium = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AC") {
                                        if($cell->getValue()!=NULL) {
                                            $payout_basis = $cell->getValue();
                                        } else {
                                            $payout_basis = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AD") {
                                        if($cell->getValue()!=NULL) {
                                            $commissionable_amount = $cell->getValue();
                                        } else {
                                            $commissionable_amount = NULL;
                                        }
                                    } elseif($cell->getColumn()=="AE") {
                                        if($cell->getValue()!=NULL) {
                                            $percentage = $cell->getValue();
                                        } else {
                                            $percentage = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AF")) {
                                        if($cell->getValue()!=NULL) {
                                            if ($cell->getRow()==12) {
                                                $amount = $totalAmount;
                                            } else {
                                                $amount = $cell->getValue();
                                            }
                                        } else {
                                            $amount = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AG")) {
                                        if($cell->getValue()!=NULL) {
                                            $payout_to_lg = $cell->getValue();
                                        } else {
                                            $payout_to_lg = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AH")) {
                                        if($cell->getValue()!=NULL) {
                                            $payment_ecvd_month = $cell->getValue();
                                        } else {
                                            $payment_ecvd_month = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AI")) {
                                        if($cell->getValue()!=NULL) {
                                            $engine_no = $cell->getValue();
                                        } else {
                                            $engine_no = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AJ")) {
                                        if($cell->getValue()!=NULL) {
                                            $chassis_no = $cell->getValue();
                                        } else {
                                            $chassis_no = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AK")) {
                                        if($cell->getValue()!=NULL) {
                                            $previous_insurance_company = $cell->getValue();
                                        } else {
                                            $previous_insurance_company = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AL")) {
                                        if($cell->getValue()!=NULL) {
                                            $previous_insurance_no = $cell->getValue();
                                        } else {
                                            $previous_insurance_no = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AM")) {
                                        if($cell->getValue()!=NULL) {
                                            $rc_number = $cell->getValue();
                                        } else {
                                            $rc_number = NULL;
                                        }
                                    } elseif(($cell->getColumn()=="AN")) {
                                        if($cell->getValue()!=NULL) {
                                            $pincode = $cell->getValue();
                                        } else {
                                            $pincode = NULL;
                                        }
                                    }
                                }
                                $i++;
                            }

                            // echo '</tr>' . PHP_EOL;
                            // echo '</tr>' . PHP_EOL;

                            if($insertFlag!=1) {

                                if(($month==NULL) && ($timestamp==NULL) && ($advisor_name==NULL) && ($advisor_contact_no==NULL) && ($vehicle_type==NULL) && ($vehicle_model==NULL) && ($variant==NULL) && ($vehicle_rc==NULL) && ($previous_insurance==NULL) && ($claim_in_previous_year==NULL) && ($customer_name==NULL) && ($owner_contact_no==NULL) && ($owner_mail_id==NULL) && ($owner_date_of_birth==NULL) && ($nominee_name==NULL) && ($nominee_date_of_birth==NULL) && ($relation_with_nominee==NULL) && ($policy_type==NULL) && ($addon_required==NULL) && ($policy_status==NULL) && ($policy_no==NULL) && ($payment_date==NULL) && ($policy==NULL) && ($policy_expiry_date==NULL) && ($insurance_company==NULL) && ($premium_without_gst==NULL) && ($gst==NULL) && ($total_premium==NULL) && ($payout_basis==NULL) && ($commissionable_amount==NULL) && ($percentage==NULL) && ($amount==NULL) && ($payout_to_lg==NULL) && ($payment_ecvd_month==NULL) && ($engine_no==NULL) && ($chassis_no==NULL) && ($previous_insurance_company==NULL) && ($previous_insurance_no==NULL) && ($rc_number==NULL) && ($pincode==NULL)) 
                                {
                                    continue;
                                }

                                $policyType=null;
                                $policy_type = strtolower($policy_type);
                                $comprehensive = strpos($policy_type,"comprehensive");
                                $thirdparty = strpos($policy_type,"third");
                                $standalone = strpos($policy_type,"standalone");

                                if (($comprehensive==true)) {
                                    $policyType = 'c';
                                }

                                if (($thirdparty==true)) {
                                    $policyType = 't';
                                }

                                if (($standalone==true)) {
                                    $policyType = 's';
                                }

                                if (isset($month) && ($month!=NULL)) {
                                    $month = date('Y-m-d H:i:s', strtotime($month));
                                }

                                if (isset($timestamp) && ($timestamp!=NULL)) {
                                    $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
                                }

            
                                // $advisor_name = "Firoz";
                                $advisorCount = User::where('first_name', 'like', "%$advisor_name%")->count();
                                if ($advisorCount==0) {
                                    $user = new User;
                                    $user->created_at = $month;
                                    $user->updated_at = $timestamp;
                                    $user->first_name = $advisor_name;
                                    $user->mobile_no = $advisor_contact_no;
                                    $user->password = Hash::make("12345678");
                                    $user->save();
                                    $advisor_id = $user->id;

                                    $leads = new leads;
                                    $leads->created_at = $month;
                                    $leads->updated_at = $timestamp;
                                    $leads->assign_to = $advisor_id;
                                    $leads->save();
                                } else {
                                    $user = User::where('first_name', 'like', "%$advisor_name%")->first('id')->toArray();
                                    $advisor_id = ($user['id']) ? $user['id'] : "";
                                    $leads = new leads;
                                    $leads->created_at = $month;
                                    $leads->updated_at = $timestamp;
                                    $leads->assign_to = $advisor_id;
                                    $leads->save();
                                }


                                $lead_profiles = new lead_profiles;
                                $lead_profiles->full_name = $customer_name;
                                $lead_profiles->mobile_no = $owner_contact_no;
                                $lead_profiles->email = $owner_mail_id;
                                $lead_profiles->dob = $owner_date_of_birth;
                                $lead_profiles->nominee_name = $nominee_name;
                                $lead_profiles->nominee_dob = $nominee_date_of_birth;
                                $lead_profiles->nominee_relation = $relation_with_nominee;
                                $lead_profiles->save();
                                $str = "LEADPRFLE";
                                $lead_profilesId = str_pad($str,15,0);
                                $lead_profiles->unique_id = $lead_profilesId.$lead_profiles->id;
                                $lead_profiles->save();
                                

                                // Insert into InsuranceMIS Table.
                                $leadVehicles = new lead_vehicles;
                                $leadVehicles->maker_desc = $vehicle_type;
                                $leadVehicles->maker_model = $vehicle_model;
                                $leadVehicles->vh_class_desc = $variant;
                                $leadVehicles->rc_img = $vehicle_rc;
                                $leadVehicles->policy_type = $policyType;
                                $leadVehicles->insurance_policy_no = $policy_no;
                                // $leadVehicles->policy_img = $policy;
                                $leadVehicles->policy_img = $previous_insurance;
                                $leadVehicles->insurance_comp = $insurance_company;
                                $leadVehicles->eng_no = $engine_no;
                                $leadVehicles->chasi_no = $chassis_no;
                                $leadVehicles->insurance_upto = $policy_expiry_date;
                                $leadVehicles->save();
                                $str = "LEADVHCLE";
                                $leadVehiclesId = str_pad($str,15,0);
                                $leadVehicles->unique_id = $leadVehiclesId.$leadVehicles->id;
                                $leadVehicles->save();

                                $wallet = new Wallet;
                                $wallet->lead_id = $leads->id;
                                $wallet->premium_without_gst = $premium_without_gst;
                                $wallet->gst = $gst;
                                $wallet->total_premium = $total_premium;
                                $wallet->payout_basis = $payout_basis;
                                $wallet->commissionable_amount = $commissionable_amount;
                                $wallet->percentage = $percentage;
                                $wallet->amount = $amount;
                                $wallet->payout_to_lg = $payout_to_lg;
                                $wallet->payment_recvd_month = $payment_ecvd_month;
                                $str = "WLLT";
                                $walletId = str_pad($str,8,0);
                                $wallet->unique_id = $walletId.$wallet->id;
                                $wallet->save();

                            //     /* 
                            //     $insuranceMis = new InsuranceMis;
                            //     $insuranceMis->month = $month;
                            //     $insuranceMis->timestamp = $timestamp;
                            //     $insuranceMis->advisor_name = $advisor_name;
                            //     $insuranceMis->advisor_contact_no = $advisor_contact_no;
                            //     $insuranceMis->vehicle_type = $vehicle_type;
                            //     $insuranceMis->vehicle_model = $vehicle_model;
                            //     $insuranceMis->variant = $variant;
                            //     $insuranceMis->vehicle_rc = $vehicle_rc;
                            //     $insuranceMis->previous_insurance = $previous_insurance;
                            //     $insuranceMis->claim_in_previous_year = $claim_in_previous_year;
                            //     $insuranceMis->customer_name = $customer_name;
                            //     $insuranceMis->owner_contact_no = $owner_contact_no;
                            //     $insuranceMis->owner_mail_id = $owner_mail_id;
                            //     $insuranceMis->owner_date_of_birth = $owner_date_of_birth;
                            //     $insuranceMis->nominee_name = $nominee_name;
                            //     $insuranceMis->nominee_date_of_birth = $nominee_date_of_birth;
                            //     $insuranceMis->relation_with_nominee = $relation_with_nominee;
                            //     $insuranceMis->policy_type = $policy_type;
                            //     $insuranceMis->addon_required = $addon_required;
                            //     $insuranceMis->policy_status = $policy_status;
                            //     $insuranceMis->policy_no = $policy_no;
                            //     $insuranceMis->payment_date = $payment_date;
                            //     $insuranceMis->policy = $policy;
                            //     $insuranceMis->policy_expiry_date = $policy_expiry_date;
                            //     $insuranceMis->insurance_company = $insurance_company;
                            //     $insuranceMis->premium_without_gst = $premium_without_gst;
                            //     $insuranceMis->gst = $gst;
                            //     $insuranceMis->total_premium = $total_premium;
                            //     $insuranceMis->payout_basis = $payout_basis;
                            //     $insuranceMis->commissionable_amount = $commissionable_amount;
                            //     $insuranceMis->percentage = $percentage;
                            //     $insuranceMis->amount = $amount;
                            //     $insuranceMis->payout_to_lg = $payout_to_lg;
                            //     $insuranceMis->payment_ecvd_month = $payment_ecvd_month;
                            //     $insuranceMis->engine_no = $engine_no;
                            //     $insuranceMis->chassis_no = $chassis_no;
                            //     $insuranceMis->previous_insurance_company = $previous_insurance_company;
                            //     $insuranceMis->previous_insurance_no = $previous_insurance_no;
                            //     $insuranceMis->rc_number = $rc_number;
                            //     $insuranceMis->pincode = $pincode;
                            //     $insuranceMis->save();
                            //     */
                            }
                            
                            // $insertFlag++;
                        }

                        

                        // echo '</table>' . PHP_EOL;
                        return redirect()->route('crm.importpayout')->with('success', 'Insurance MIS Data imported successfully.');
                    }
                    else
                    {
                        return redirect()->route('crm.importpayout')->with('error', 'Opps! Specified Sheet does not exists in the excel..');
                    }
                }
                else
                {
                    return redirect()->route('crm.importpayout')->with('error', 'Sheet is empty.');
                }
            } elseif ($file_type==4) {

                $inputFileName = $file->getRealPath();
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

                $sheetCount = $spreadsheet->getSheetCount();
                $sheetNames = $spreadsheet->getSheetNames();

                if($sheetCount > 0)
                {
                    if (in_array("Vehicle insurance", $sheetNames))
                    {
                        $worksheet = $spreadsheet->getSheetByName('Vehicle insurance');
                        // $worksheet->setCellValue('AF12', '=SUM(AF2:AF11)');
                        // $totalAmount = $worksheet->getCell('AF12')->getCalculatedValue();

                        $j=1;
                        foreach ($worksheet->getRowIterator() as $row) {
                            echo '<tr>' . PHP_EOL;
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                                            //    even if a cell value is not set.
                                                                            // For 'TRUE', we loop through cells
                                                                            //    only when their value is set.
                                                                            // If this method is not called,
                                                                            //    the default value is 'false'.
                        
                            // $headers = array();
                            foreach ($cellIterator as $cell) {
                                // var_dump($cell->getRow());
                                if ($cell->getRow()==1) {
                                    // echo '<th>';
                                    // if (trim($cell->getValue())!="") { echo $j . ' -> ' . $cell->getValue(); echo ' | '; }
                                    if (trim($cell->getValue())!="") { array_push($this->headers,$cell->getValue()); }
                                    // echo '</th>';
                                    

                                    // array_push($headers,$cell->getValue());
                                } else {
                                    // echo '<td>' .
                                    // $cell->getValue() .
                                    // '</td>' . PHP_EOL;
                                }
                                $j++;
                            }
                            echo '</tr>' . PHP_EOL;
                            // echo "<pre>";
                            // print_r($headers);
                            // echo "</pre>";
                            // die(' here ');
                        }
                    }
                }
            } elseif ($file_type==5) {
                echo "<pre>";
                print_r($this->headers);
                echo "</pre>";
                die(' headers ');

                echo "<pre>";
                print_r($this->insurance);
                echo "</pre>";
                die(' here ');
            }

            // return redirect()->route('crm.importpayout')->with('success', 'Data imported successfully.');
        } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return redirect()->route('crm.importpayout')->with('error', $e->getMessage());
        }
    }



    // Compare Sheets Fields
    public function compareSheets(Request $request)
    {
        return view('crm.payouts.comparesheets');
    }


    public function sheetResults(Request $request)
    {
        $file = $request->file('sheets');

        // First Excel
        $first = $file[0];
        $firstInputFileName = $first->getRealPath();
        $firstspreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($firstInputFileName);
        $firstsheetCount1 = $firstspreadsheet->getSheetCount();
        $firstsheetNames1 = $firstspreadsheet->getSheetNames();

        // Second Excel
        $second = $file[1];
        $secondInputFileName = $second->getRealPath();
        $secondspreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($secondInputFileName);
        $secondsheetCount2 = $secondspreadsheet->getSheetCount();
        $secondsheetNames2 = $secondspreadsheet->getSheetNames();
      
        if($firstsheetCount1 > 1) {
            $worksheet1 = $firstspreadsheet->getSheet(1);
        } else {
            $worksheet1 = $firstspreadsheet->getSheet(0);
        }

        if($secondsheetCount2 > 1) {
            $worksheet2 = $secondspreadsheet->getSheet(1);
        } else {
            $worksheet2 = $secondspreadsheet->getSheet(0);
        }


        // $worksheet = $spreadsheet->getSheetByName('Used Vehicle');
        $insertFlag = 1;
        $array1 = [];
        foreach ($worksheet1->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                            //    even if a cell value is not set.
                                                            // For 'TRUE', we loop through cells
                                                            //    only when their value is set.
                                                            // If this method is not called,
                                                            //    the default value is 'false'.
            
            foreach ($cellIterator as $cell) {
                if ($cell->getRow()==1) {
                    echo '<th>';
                    if (trim($cell->getValue())!="") {
                        // $cell->getValue();
                        $array1[] = $this->simplifiedString($cell->getValue());

                        // $usedVehicleArr[] = Str::slug($cell->getValue(), '_');
                        // if (in_array(Str::slug($cell->getValue(), '_'), $usedVehicleArr))
                        // { 
                        //     echo Str::slug($cell->getValue(), '_');
                            
                        // }
                    }
                    echo '</th>';
            
                }

            }

        }

        echo '<hr>';

        $array2 = [];
        foreach ($worksheet2->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                            //    even if a cell value is not set.
                                                            // For 'TRUE', we loop through cells
                                                            //    only when their value is set.
                                                            // If this method is not called,
                                                            //    the default value is 'false'.
            
            foreach ($cellIterator as $cell) {
                if ($cell->getRow()==1) {
                    // echo '<th>';
                    if (trim($cell->getValue())!="") {
                        $array2[] = $this->simplifiedString($cell->getValue());
                    }
                    // echo '</th>';
                }
            }
        }

        $intersect = array_intersect($array1, $array2);
        $intersect = isset($intersect) ? $intersect : array();
        $diff = array_diff($array1, $array2);
        $diff = isset($diff) ? $diff : array();

        $cdata = array('intersect' => $intersect, 'diff' => $diff);

        // print_r($data); die(' sss ');
        return view('crm.payouts.comparisonResult', compact('intersect','diff','cdata'));
        
    }




    function sanitizeString($string='')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }


    public function sampleFileDownload()
    {
        $id = $_POST['file'];
        if($id==1) {
            $file = public_path('excels/Used_Vehicle_MIS.xlsx');
            $fileName = "used_vehicle_mis";
        } elseif ($id==2) {
            $file = public_path('excels/credit_card_request_form_responses.xlsx');
            $fileName = "credit_card_request_form_responses";
        } elseif ($id==3) {
            $file = public_path('excels/Insurance_sheet_mis.xlsx');
            $fileName = "Insurance_sheet_mis";
        }
        $headers = array( "Content-type: application/vnd.ms-excel; charset=UTF-8" );
        return Response::download($file, $fileName.'.xlsx', $headers);
    }


    function simplifiedString($string='')
    {
        $arr = explode('|', $string);
        if(count($arr) == 2) {
            $string = $arr[0];
        }
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = trim($string);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = strtolower($string);
        return $string;

    }

    function uniqueString($string='')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = str_replace(' ', '', $string); // Replaces all spaces.
        $string = strtolower($string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }


}
