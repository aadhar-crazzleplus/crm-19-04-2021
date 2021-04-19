<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePincodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('circle_name',20)->nullable()->default('NULL');
            $table->string('region_name',20)->nullable()->default('NULL');
            $table->string('division_name',20)->nullable()->default('NULL');
            $table->string('office_name',20)->nullable()->default('NULL');
            $table->integer('pincode')->nullable()->default(0);
            $table->string('office_type',20)->nullable()->default('NULL');
            $table->string('delivery',20)->nullable()->default('NULL');
            $table->integer('created_by')->nullable()->default(0);
            $table->integer('modified_by')->nullable()->default(0);
            $table->string('district',20)->nullable()->default('NULL');
            $table->string('district_name',20)->nullable()->default('NULL');
            $table->foreignId('city_id')->nullable()->constrained('cities');

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
        Schema::dropIfExists('pincodes');
    }
}
