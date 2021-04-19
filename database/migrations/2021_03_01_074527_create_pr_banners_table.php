<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_banners', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("image");
            $table->foreignId('updated_by')->nullable()->constrained('admins');
            $table->string("url")->nullable();
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
        Schema::dropIfExists('pr_banners');
    }
}
