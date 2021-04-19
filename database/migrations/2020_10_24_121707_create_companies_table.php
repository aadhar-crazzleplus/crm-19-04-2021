<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_cat_id')->nullable()->constrained('company_cats');
            $table->foreignId('org_id')->nullable()->constrained('organizations');
            $table->foreignId('bank_id')->nullable()->constrained('banks');
            $table->string('company_code',100)->nullable();
            $table->string('company_name',200)->nullable();
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('state_id')->constrained('states');
            $table->string('address')->nullable();
            $table->foreignId('pincode_id')->nullable()->constrained("pincodes");
            $table->string('phone_number',50)->nullable();
            $table->enum('status',['1','0'])->default('1');
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
        Schema::dropIfExists('companies');
    }
}
