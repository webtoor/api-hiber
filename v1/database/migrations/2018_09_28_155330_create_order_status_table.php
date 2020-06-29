<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->integer('changedby_id')->unsigned();
            $table->integer('doneby_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('rf_status')->onDelete('cascade');
            $table->foreign('changedby_id')->references('id')->on('rf_users')->onDelete('cascade');
            $table->foreign('doneby_id')->references('id')->on('rf_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status');
    }
}
