<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitorsstatistics_visitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip', 46);
            $table->string('country', 64);
            $table->string('city', 128);
            $table->string('device', 32);
            $table->string('browser', 128);
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
        Schema::dropIfExists('visitorsstatistics_visitors');
    }
}
