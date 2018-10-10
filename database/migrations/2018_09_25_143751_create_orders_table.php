<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->integer('createdby')->unsigned();
            $table->date('dtprojectstart');
            $table->date('dtprojectend');
            $table->integer('projecttype')->unsigned();
            $table->integer('orderhourduration');
            $table->text('comment');
            $table->timestamps();
            $table->foreign('createdby')->references('id')->on('rf_users')->onDelete('cascade');
            $table->foreign('projecttype')->references('id')->on('rf_projecttypes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
