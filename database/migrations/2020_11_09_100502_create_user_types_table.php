<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('title',200)->nullable();
            $table->enum('admin_access',['y','n'])->nullable()->default('n');
            $table->timestamps();
        });

        $data = [
            ['title'=>'Super Admin', 'admin_access'=>'y'],
            ['title'=>'Admin', 'admin_access'=>'y'],
            ['title'=>'Coordinator', 'admin_access'=>'y'],
            ['title'=>'National Head', 'admin_access'=>'y'],
            ['title'=>'Zonal Head', 'admin_access'=>'y'],
            ['title'=>'State Head', 'admin_access'=>'y'],
            ['title'=>'Cluster Head', 'admin_access'=>'y'],
            ['title'=>'City Head', 'admin_access'=>'y'],
            ['title'=>'Business Development Manager', 'admin_access'=>'y'],
            ['title'=>'Advisor', 'admin_access'=>'n'],
            ['title'=>'Manager', 'admin_access'=>'n'],
        ];

        DB::table('user_types')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_types');
    }
}
