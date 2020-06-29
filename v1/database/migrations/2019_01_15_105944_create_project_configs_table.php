<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id')->unsigned();
            $table->integer('projecttype_id')->unsigned();
            $table->integer('config_id')->unsigned();
            $table->double('value');
            $table->foreign('source_id')->references('id')->on('rf_sources')->onDelete('cascade');
            $table->foreign('projecttype_id')->references('id')->on('rf_projecttypes')->onDelete('cascade');
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
        Schema::dropIfExists('project_configs');
    }
}
