<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
		$table->string('card_name',200)->nullable();
		$table->foreignId('bank_id')->constrained('banks');
		$table->enum('card_type',['credit','debit'])->default('credit');
		$table->foreignId('card_network_id')->constrained('card_networks');
		$table->string('joining_fees',50)->nullable();
		$table->string('renewal_fees',50)->nullable();
		$table->string('min_late_pay_fee',50)->nullable();
		$table->string('max_late_pay_fee',50)->nullable();
		$table->string('min_age',50)->nullable();
		$table->string('max_age',50)->nullable();
		$table->text('rewards')->nullable();
		$table->string('seo_title')->nullable();
		$table->string('seo_key')->nullable();
		$table->string('seo_des')->nullable();
		$table->string('image_url')->nullable();
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
        Schema::dropIfExists('cards');
    }
}
