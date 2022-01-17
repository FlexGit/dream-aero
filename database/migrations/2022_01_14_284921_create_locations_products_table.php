<?php

use App\Models\FlightSimulator;
use App\Models\Location;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsFlightSimulatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations_flight_simulators', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('location_id')->nullable(false)->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			$table->unsignedBigInteger('flight_simulator_id')->nullable(false)->index();
			$table->foreign('flight_simulator_id')->references('id')->on('flight_simulators')->onDelete('cascade');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->timestamps();
			$table->softDeletes();
        });
	
		$locations = Location::get();
	
		// 737 NG
		$flightSimulatorEntity = HelpFunctions::getEntityByAlias(FlightSimulator::class, FlightSimulator::ALIAS_737);
	
		foreach ($locations as $location) {
			$data = [];
		
			switch ($location->alias) {
				case 'msk_afi':
					$data['events'] = [
						'shift_admin' => '#92E1C1',
						'shift_pilot' => '#92E1C1',
						'deal_paid' => '#92E1C1',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'msk_bus':
					$data['events'] = [
						'shift_admin' => '#8E24AA',
						'shift_pilot' => '#8E24AA',
						'deal_paid' => '#8E24AA',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'msk_veg':
					$data['events'] = [
						'shift_admin' => '#65BA9B',
						'shift_pilot' => '#65BA9B',
						'deal_paid' => '#65BA9B',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'piter_piterland':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'piter_ohta':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'piter_rio':
					$data['events'] = [
						'shift_admin' => '#92E1C1',
						'shift_pilot' => '#92E1C1',
						'deal_paid' => '#92E1C1',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'ekb_alatyr':
					$data['events'] = [
						'shift_admin' => '#86E7EC',
						'shift_pilot' => '#86E7EC',
						'deal_paid' => '#86E7EC',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'khv_brosko':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'krd_sbs_megamall':
					$data['events'] = [
						'shift_admin' => '#009788',
						'shift_pilot' => '#009788',
						'deal_paid' => '#009788',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'kzn_parkhouse':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'nnv_zharptitsa':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'nsk_siberian':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'sam_kosmoport':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'vrn_chizhova':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
			}
			
			$location->simulators()->attach($flightSimulatorEntity->id, ['data_json' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
		}
	
		// A320
		$flightSimulatorEntity = HelpFunctions::getEntityByAlias(FlightSimulator::class, FlightSimulator::ALIAS_A320);

		foreach ($locations as $location) {
			if (!in_array($location->alias, ['msk_afi', 'piter_ohta'])) {
				continue;
			}
			$data = [];
			
			switch ($location->alias) {
				case 'msk_afi':
					$data['events'] = [
						'shift_admin' => '#A3879B',
						'shift_pilot' => '#A3879B',
						'deal_paid' => '#A3879B',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
				case 'piter_ohta':
					$data['events'] = [
						'shift_admin' => '#018B58',
						'shift_pilot' => '#018B58',
						'deal_paid' => '#018B58',
						'deal_notpaid' => '#7986CB',
						'note' => '#F6BF26',
					];
				break;
			}
			
			$location->simulators()->attach($flightSimulatorEntity->id, ['data_json' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
		}
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations_flight_simulators');
    }
}
