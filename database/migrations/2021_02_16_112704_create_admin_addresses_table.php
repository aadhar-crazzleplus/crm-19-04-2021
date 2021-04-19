<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('admin_id')->constrained('admins');
            $table->enum('address_type',['res','per','ren','off','oth'])->default('res')->comment('Address type: Residence, Permanent Address, Rented, Office Address, Other Address');
            $table->string('add1')->nullable();
            $table->string('add2')->nullable();
            $table->foreignId('pincode_id')->nullable()->constrained('pincodes');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->enum('is_current',['n','y'])->nullable()->default('n');
            $table->enum('add_proof',['p','d','a','v','r','o'])->nullable()->default('d')->comment('Passport, Driving Licence, UID Aadhar, Voter Id, ration card, others');
            $table->string('add_proof_no',100)->nullable();
            $table->date('add_proof_isu_date')->nullable()->comment('address proof issue date');
            $table->date('add_proof_exp_date')->nullable()->comment('address proof expiry date');
            $table->string('id_doc_front')->nullable();
            $table->string('id_doc_back')->nullable();
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
        Schema::dropIfExists('admin_addresses');
    }
}
