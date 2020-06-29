<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('config_id')->unsigned();
            $table->string('value');
            $table->string('note');
            $table->date('dt_created');
            $table->foreign('user_id')->references('id')->on('rf_users')->onDelete('cascade');
            $table->foreign('config_id')->references('id')->on('rf_configs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_configs');
    }
}
