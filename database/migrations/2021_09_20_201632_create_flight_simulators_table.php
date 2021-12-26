<?php

use App\Models\FlightSimulator;
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
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$items = [
			'0' => [
				'name' => 'BOEING 737 NG',
			],
			'1' => [
				'name' => 'AIRBUS A320',
			],
		];
	
		foreach ($items as $item) {
			$flightSimulator = new FlightSimulator();
			$flightSimulator->name = $item['name'];
			$flightSimulator->save();
		}
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
