<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialCardRelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_card_rels', function (Blueprint $table) {
            $table->id();
            $table->foreignId("social_card_id")->nullable()->constrained("social_cards");
            $table->foreignId("social_cat_id")->nullable()->constrained("social_card_cats");
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
        Schema::dropIfExists('social_card_rels');
    }
}
