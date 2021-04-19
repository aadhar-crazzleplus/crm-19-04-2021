<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('admin_by')->nullable()->constrained('admins')->comment('admin user id who added lead');
            $table->foreignId('lead_by')->nullable()->constrained('users')->comment('advisor user id who added lead');
            $table->foreignId('assign_to')->nullable()->constrained('users')->comment('advisor user id whom assign lead');
            $table->foreignId('close_by')->nullable()->constrained('users')->comment('advisor user id who closed lead');
            $table->foreignId('updated_by')->nullable()->constrained('admins')->comment('admin user id who updated lead');
            $table->foreignId('lead_profile_id')->nullable()->constrained('lead_profiles');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->string('lead_remark',255)->nullable();
            $table->enum('lead_status',['i','p','c','r','d'])->nullable()->default('i')->comment('incomplete, processing, closed, rejected, deleted');
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
        Schema::dropIfExists('leads');
    }
}
