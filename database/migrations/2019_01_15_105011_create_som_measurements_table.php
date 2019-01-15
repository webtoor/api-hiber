<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('som_measurements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('som_id')->unsigned();
            $table->integer('measurement_id')->unsigned();
            $table->double('ratio');
            $table->string('measurement_display');
            $table->foreign('som_id')->references('id')->on('rf_soms')->onDelete('cascade');
            $table->foreign('measurement_id')->references('id')->on('rf_measurements')->onDelete('cascade');

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('som_measurements');
    }
}
