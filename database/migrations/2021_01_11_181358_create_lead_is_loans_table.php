<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadIsLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_is_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_profile_id')->constrained('lead_profiles');
            $table->string('total_rem_loan')->nullable();
            $table->string('monthly_emi')->nullable();
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
        Schema::dropIfExists('lead_is_loans');
    }
}
