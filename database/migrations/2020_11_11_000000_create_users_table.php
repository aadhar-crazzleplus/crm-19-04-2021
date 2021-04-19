<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     $table->rememberToken();
        //     $table->foreignId('current_team_id')->nullable();
        //     $table->text('profile_photo_path')->nullable();
        //     $table->timestamps();
        // });
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',50)->nullable();
            $table->string('middle_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('email',50)->nullable()->unique();
            $table->string('mobile_no',50);
            $table->string('alt_mobile',50)->nullable();
            $table->string('otp',50)->nullable();
            $table->string('email_otp',50)->nullable();
            $table->enum('marital_status', ['m','s','o'])->default('m')->comment('married, single, others');
            $table->enum('citizenship', ['i','n','f'])->default('i')->comment('indian, nri, foreigner');
            $table->date('dob')->nullable();
            $table->enum('gender', ['m','f','o'])->default('m')->comment('male,female,other');
            $table->string('father_name',50)->nullable();
            $table->string('mother_name',50)->nullable();
            $table->string('spouse_name',50)->nullable();
            $table->string('nominee_name',50)->nullable();
            $table->string('nominee_relation',50)->nullable();
            $table->date('nominee_dob',50)->nullable();
            $table->string('pan_no',50)->nullable();
            $table->string('upload_pan_no')->nullable();
            $table->string('gst_no',50)->nullable();
            $table->string('upload_gst_no')->nullable();
            $table->smallInteger('dependents')->nullable()->default(0);
            $table->enum('qualification',['hs','ug','pg','pr','ot'])->default('hs')->comment('hs=high school,ug=undergraduate,pg=postgraduate,pr=professionals,ot=others');
            $table->string('upload_qual_doc')->nullable()->comment('Upload qualification document');
            $table->enum('res_status',['ri','nri','fn','poi'])->default('ri')->comment('Residential Individual, Non-Resident Indian, Foreign National, Person of India Origin');
            $table->foreignId('occupation_id')->nullable()->constrained('occupations')->comment('Work Details: Salaried, Self-employed, etc');
            $table->foreignId('pos_income_id')->nullable()->constrained('pos_incomes')->comment('Work Details: Insurance Agent/Advisor, Financial Advisor/Consultant, Direct Selling Associate, etc');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->comment('Industry /Sector: Advertising/ Marketing, Agriculture,etc');
            $table->foreignId('grade_id')->nullable()->constrained('grades')->comment('Grade: 1st grade/second grade/Etc');
            $table->foreignId('busi_type_id')->nullable()->constrained('busi_types')->comment('Proprietorship/Partnership/Pvt ltd company/HUF');
            $table->foreignId('profession_id')->nullable()->constrained('professions')->comment('Architect/ CA/ Consultant/ Doctor/ Lawyer/ Others');
            $table->string('net_mon_incm',50)->nullable()->comment('Net Monthly Income');
            $table->string('net_yr_incm',50)->nullable()->comment('Net Yearly Income');
            $table->string('gros_mon_incm',50)->nullable()->comment('Gross Monthly Income');
            $table->string('gros_yr_incm',50)->nullable()->comment('Gross Yearly Income');
            $table->smallInteger('cur_job_year')->nullable()->comment('Duration at current Job in years');
            $table->smallInteger('cur_job_month')->nullable()->comment('Duration at current Job in months');
            $table->smallInteger('total_ex_yr')->nullable()->comment('Total Work experience in Years');
            $table->smallInteger('total_ex_month')->nullable()->comment('Total Work experience in Months');
            $table->smallInteger('total_bus_yr')->nullable()->comment('Total Duration In business in years');
            $table->smallInteger('total_bus_month')->nullable()->comment('Total Duration In business in months');
            $table->smallInteger('total_fn_yr')->nullable()->comment('Total Duration In financial product in months');
            $table->smallInteger('total_fn_month')->nullable()->comment('Total Duration In financial product in months');

            $table->enum('office_space',['n','y'])->default('n')->comment('do you have office space?');
            $table->enum('pos_licence',['n','y'])->default('n')->comment('Do you have agency or POS licence');
            $table->string('total_bus_anum')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('admins')->comment('Admin/coordinator user id');
            $table->string('user_code')->nullable()->comment('unique advisor/user code');
            $table->enum('are_you',['none','solo','company'])->default('none')->comment('what are you existing: advisor/company');
            $table->string('firm_name',255)->nullable()->comment('Name of the Firm');
            $table->enum('smoker_or_chewer',['none','no','yes'])->default('none');
            $table->string('referral_code',50)->nullable()->comment('own referral code');
            $table->foreignId('referred_by')->nullable()->constrained('users')->comment('user id who refer you');
            $table->foreignId('user_type_id')->nullable()->constrained('user_types')->comment('user type id ');
            // $table->enum('user_type',['user','advisor','coordinator','super','admin','nh','zh','sh','ch','cth','bdm'])->default('user');
            $table->enum('user_status',['0','1','2'])->default('1')->comment('0=deleted, 1=unverified, 2=verified');
            $table->string('profile_photo',200)->nullable();
            $table->string('fcm_token',255)->nullable();
            $table->string('per_lats_longs',255)->nullable()->comment("Permanent lat-logs");
            $table->string('cur_lats_longs',255)->nullable()->comment("Current lat-logs");
            $table->enum('bsoffice_type',['bso','bro','pos','ind'])->nullable()->default('ind')->comment('bso=banksathi office, bro=branch office, pos=pos office, ind=independent');
            $table->enum('is_mpin',['y','n'])->nullable()->default('n');
            $table->string('kyc_video')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
