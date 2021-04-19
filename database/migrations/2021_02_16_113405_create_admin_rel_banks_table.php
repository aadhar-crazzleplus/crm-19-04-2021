<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminRelBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_rel_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks');
            $table->foreignId('admin_id')->constrained('admins');
            $table->string('name_on_bank',100)->nullable();
            $table->string('ifsc_code',100)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_no',100)->nullable();
            $table->string('customer_id',100)->nullable();
            $table->enum('uploads',['cheque','pass']);
            $table->string('upload_doc')->nullable()->comment('upload cheque or passbook');
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
        Schema::dropIfExists('admin_rel_banks');
    }
}
