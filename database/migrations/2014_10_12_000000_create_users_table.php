<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('first_name', 20)->nullable();
            $table->string('last_name', 40)->nullable();
            $table->string('country', 100)->nullable()->default('Україна');
            $table->string('city', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('role')->nullable()->default('Worker');
            $table->timestamps();
            $table->softDeletes();

        });

        //
        //Insert Admin
        //
         DB::table('users')->insert(
        array(
            'email' => 'admin@localhost',
            'password' => Hash::make(123456),
            'role' => 'Admin'
        )
    );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
