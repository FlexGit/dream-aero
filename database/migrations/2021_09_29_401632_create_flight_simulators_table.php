<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightSimulatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_simulators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('наименование авиатренажера');
			$table->integer('flight_simulator_type_id')->default(0)->index()->comment('тип авиатренажера');
			$table->integer('location_id')->default(0)->index()->comment('локация, в которой находится авиатренажер');
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
        Schema::dropIfExists('flight_simulators');
    }
}
