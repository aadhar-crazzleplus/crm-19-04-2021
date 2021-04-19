<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_cards', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->string("image")->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('admins');
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
        Schema::dropIfExists('social_cards');
    }
}
