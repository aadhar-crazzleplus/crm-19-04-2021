<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('lead_profile_id')->nullable()->constrained('lead_profiles');
            $table->string("address",255)->nullable();
            $table->foreignId("pincode_id")->nullable()->constrained("pincodes");
            $table->foreignId("city_id")->nullable()->constrained("cities");
            $table->foreignId("state_id")->nullable()->constrained("states");
            $table->enum("is_current",['yes','no'])->nullable();
            $table->enum("add_type",['o','r'])->nullable()->comment("o=owned, r=rented");
            $table->smallInteger('cur_add_year')->nullable()->comment('Duration at current Address in years');
            $table->smallInteger('cur_add_month')->nullable()->comment('Duration at current Address in months');
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
        Schema::dropIfExists('lead_addresses');
    }
}
