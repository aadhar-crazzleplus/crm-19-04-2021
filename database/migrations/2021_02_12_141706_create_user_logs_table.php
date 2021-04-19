<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_logs', function (Blueprint $table) {
            // $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('admin_id')->nullable()->constrained('admins');
            $table->string('ip')->nullable();
            $table->string('local_ip')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('mobile_tem')->nullable();
            $table->string('signature')->nullable();
            $table->string('time')->nullable();
            $table->string('url')->nullable();
            $table->datetime('activity_at')->nullable();
            $table->string('lags_longs')->nullable();
            $table->string('device_id')->nullable();
            $table->string('app_version')->nullable();
            $table->string('mobile_type')->nullable();
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
        Schema::dropIfExists('user_logs');
    }
}
