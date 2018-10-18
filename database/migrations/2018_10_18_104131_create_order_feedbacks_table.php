<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('writter')->unsigned();
            $table->integer('for')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->integer('rating');
            $table->text('comment');
            $table->timestamps();
            $table->foreign('writter')->references('id')->on('rf_users')->onDelete('cascade');
            $table->foreign('for')->references('id')->on('rf_users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_feedbacks');
    }
}
