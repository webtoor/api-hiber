<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rf_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', '100')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('firstname', '100')->nullable();
            $table->string('lastname', '100')->nullable();
            $table->string('address', '100')->nullable();
            $table->integer('phonenumber', '13')->nullable();
            $table->string('post_code')->nullable();
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
        Schema::dropIfExists('rf_users');
    }
}
