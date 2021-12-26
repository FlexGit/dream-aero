<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationFlightSimulatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_flight_simulators', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('location_id')->nullable(false)->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			$table->unsignedBigInteger('flight_simulator_id')->nullable(false)->index();
			$table->foreign('flight_simulator_id')->references('id')->on('flight_simulators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_flight_simulators');
    }
}
