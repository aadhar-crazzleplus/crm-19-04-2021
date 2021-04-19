<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("full_name",255)->nullable();
            $table->string("mobile_no",255);
            $table->string("otp",50)->nullable();
            $table->string("email",255)->nullable();
            $table->date("dob")->nullable();
            $table->foreignId('occupation_id')->nullable()->constrained('occupations')->comment('Work Details: Salaried, Self-employed, etc');
            $table->string("monthly_salary",100)->nullable();
            $table->foreignId("company_id")->nullable()->constrained("companies");
            $table->string("designation",255)->nullable();
            $table->string("company_vintage",255)->nullable()->comment("vintage = how old");
            $table->string("office_email",255)->nullable();
            $table->string("itr_amount",255)->nullable();
            $table->string("gst_no",255)->nullable();
            $table->string("gst_vintage",255)->nullable()->comment("vintage = how old");
            $table->string("pan_no",100)->nullable();
            $table->string("pan_img",200)->nullable();
            $table->string("adhar_no",200)->nullable();
            $table->string("adhar_img",200)->nullable();
            $table->string("busi_vintage",200)->nullable()->comment("vintage = how old your business is");
            $table->string("office_setup",200)->nullable();
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
        Schema::dropIfExists('lead_profiles');
    }
}
