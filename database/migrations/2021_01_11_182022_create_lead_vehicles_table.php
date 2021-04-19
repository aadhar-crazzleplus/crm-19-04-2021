<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_profile_id')->nullable()->constrained('lead_profiles');
            $table->string('regn_no')->nullable();
            $table->date('regn_dt')->nullable();
            $table->string('chasi_no')->nullable();
            $table->string('eng_no')->nullable();
            $table->string('vh_class_desc')->nullable();
            $table->string('maker_desc')->nullable();
            $table->string('maker_model')->nullable();
            $table->string('body_type_desc')->nullable();
            $table->string('fuel_desc')->nullable();
            $table->date('fit_upto')->nullable();
            $table->string('norms_desc')->nullable();
            $table->string('insurance_comp')->nullable();
            $table->string('insurance_policy_no')->nullable();
            $table->date('insurance_upto')->nullable();
            $table->string('registered_at')->nullable();
            $table->string('manu_month_yr')->nullable();
            $table->smallInteger('owner_sr')->nullable();
            $table->string('vch_catg')->nullable();
            $table->date('pucc_upto')->nullable();
            $table->string('pucc_no')->nullable();
            $table->string('financer')->nullable();
            $table->date('status_as_on')->nullable()->comment("record fetch date");
            $table->text('api_result')->nullable();
            $table->string('rc_img')->nullable();
            $table->string('policy_img')->nullable();
            $table->enum('policy_type',["c","t","s"])->nullable()->comment("Comprehensive, Third-party, standalone od");
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
        Schema::dropIfExists('lead_vehicles');
    }
}
