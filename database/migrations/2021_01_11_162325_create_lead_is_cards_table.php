<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadIsCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_is_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_profile_id')->constrained('lead_profiles');
            $table->foreignId('bank_id')->constrained('banks');
            $table->string('total_limit')->nullable();
            $table->string('ava_limit')->nullable();
            $table->string('card_vintage')->nullable();
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
        Schema::dropIfExists('lead_is_cards');
    }
}
