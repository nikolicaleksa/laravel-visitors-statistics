<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitorsstatistics_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('value')->default(0);
            $table->enum('type', ['all', 'unique', 'max']);
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
        Schema::dropIfExists('visitorsstatistics_statistics');
    }
}
