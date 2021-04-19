<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialCardCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_card_cats', function (Blueprint $table) {
            $table->id();
            $table->foreignId("parent_id")->nullable()->constrained("social_card_cats");
            $table->string("title")->nullable();
            $table->enum("status",["1","0"])->default("1")->nullable();
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
        Schema::dropIfExists('social_card_cats');
    }
}
