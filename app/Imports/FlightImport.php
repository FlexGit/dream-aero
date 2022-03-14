<?php

namespace App\Imports;

use App\Models\Certificate;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\Location;
use App\Models\Product;
use App\Models\Score;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Row;
use Throwable;

class FlightImport implements OnEachRow, WithProgressBar
{
	use Importable;

	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
		$rowIndex = $row->getIndex();
		$row = $row->toArray();

		if ($rowIndex == 1) return null;

		$product = Product::where('name', ucfirst(trim(mb_strtolower($row[5]))))->first();
		if (!$product) {
			\Log::info('Product does not exist: ' . trim($row[5]));
			return null;
		}
	
		$city = HelpFunctions::getEntityByAlias(City::class, trim($row[8]));
		$cityId = $city ? $city->id : 0;
	
		$location = HelpFunctions::getEntityByAlias(Location::class, trim($row[9]));
		$locationId = $location ? $location->id : 0;

		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId);
		if (!$cityProduct || !$cityProduct->pivot) {
			\Log::info('CityProduct does not exist for city: ' . $cityId . ' and product:' . $product->id);
			return null;
		}
	
		if (!$cityProduct->pivot->score) {
			\Log::info('CityProduct score does not exist for city: ' . $cityId . ' and product:' . $product->id);
		}
	
		try {
			\DB::beginTransaction();
			
			$contractor = Contractor::where('email', trim(mb_strtolower($row[2])))->first();
			if (!$contractor) {
				$contractor = new Contractor();
				$contractor->name = trim(mb_strtolower($row[3]));
				$contractor->phone = preg_replace('/[^0-9,]/', '', trim($row[4]));
				$contractor->email = trim(mb_strtolower($row[2]));
				$contractor->city_id = $cityId;
				$contractor->created_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
				$contractor->updated_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
				$contractor->save();
			}

			if (trim($row[6])) {
				$certificate = Certificate::where('number', trim($row[6]))->first();
				if (!$certificate) {
					$certificate = new Certificate();
					$certificate->number = trim($row[6]);
					$certificate->status_id = 11;
					$certificate->city_id = $cityId;
					$certificate->product_id = $product->id;
					$certificate->created_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
					$certificate->updated_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
					$certificate->save();
				}
			}
			
			$deal = Deal::where('number', trim($row[0]))->first();
			if ($deal) \DB::rollback();
			
			$deal = new Deal();
			$deal->number = trim($row[0]);
			$deal->status_id = 6;
			$deal->contractor_id = $contractor->id;
			$deal->name = trim($row[3]);
			$deal->phone = preg_replace('/[^0-9,]/', '', trim($row[4]));
			$deal->email = trim(mb_strtolower($row[2]));
			$deal->created_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
			$deal->updated_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
			$deal->save();
			
			$dealPosition = new DealPosition();
			$dealPosition->number = trim($row[0]);
			$dealPosition->deal_id = $deal->id;
			$dealPosition->product_id = $product->id;
			$dealPosition->certificate_id = $certificate->id ?? 0;
			$dealPosition->duration = $product->duration;
			$dealPosition->amount = trim($row[7]);
			$dealPosition->currency_id = 1;
			$dealPosition->city_id = $cityId;
			$dealPosition->location_id = $locationId;
			$dealPosition->flight_simulator_id = 1;
			$dealPosition->flight_at = Carbon::parse(trim($row[10]))->format('Y-m-d H:i:s');
			$dealPosition->created_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
			$dealPosition->updated_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
			$dealPosition->save();
			
			$event = new Event();
			$event->event_type = Event::EVENT_TYPE_DEAL;
			$event->contractor_id = $contractor->id;
			$event->deal_id = $deal->id;
			$event->deal_position_id = $dealPosition->id;
			$event->city_id = $cityId;
			$event->location_id = $locationId;
			$event->flight_simulator_id = 1;
			$event->start_at = Carbon::parse(trim($row[10]))->format('Y-m-d H:i:s');
			$event->stop_at= (trim($row[12]) == 'Есть') ? Carbon::parse(trim($row[11]))->subMinutes(15)->format('Y-m-d H:i:s') : Carbon::parse(trim($row[11]))->format('Y-m-d H:i:s');
			$event->extra_time = (trim($row[12]) == 'Есть') ? 15 : 0;
			$event->is_repeated_flight = (trim($row[12]) == 'Есть') ? true : false;
			$event->created_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
			$event->updated_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
			$event->save();

			if (Carbon::parse(trim($row[1]))->gt(Carbon::parse('2021-03-09 00:00:00')) && Carbon::parse(trim($row[1]))->lt(Carbon::parse('2022-03-09 23:59:59'))) {
				$score = new Score();
				$score->score = $cityProduct->pivot->score;
				$score->type = Score::SCORING_TYPE;
				$score->contractor_id = $contractor->id;
				$score->deal_id = $deal->id;
				$score->deal_position_id = $dealPosition->id;
				$score->event_id = $event->id;
				$score->created_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
				$score->updated_at = Carbon::parse(trim($row[1]))->format('Y-m-d H:i:s');
				$score->save();
			}

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			\Log::debug('500 - ' . $e->getMessage() . ' - ' . implode(' | ', $row));
		}
    }
}
