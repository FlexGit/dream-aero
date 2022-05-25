<?php

namespace App\Console\Commands;

use App\Models\Contractor;
use App\Models\Discount;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Promocode;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SendPromocodeAfterFlightEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promocode_email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor with promocode after a flight, if in his city there is another type of simulator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    	// проверяем все полеты за последний час
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->whereBetween('stop_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
			->get();
    	foreach ($events as $event) {
    		if (!$event->contractor_id) continue;
		
    		$location = $event->location;
    		if (!$location) continue;
		
			$simulator = $event->simulator;
			if (!$simulator) continue;
		
			$deal = $event->deal;
			if (!$deal) continue;
			
			$position = $event->dealPosition;
			if (!$position) continue;
		
			$city = $event->city;
			if (!$city) continue;
		
			$contractor = Contractor::where('is_active', true)
				->where('email', '!=', Contractor::ANONYM_EMAIL)
				->find($event->contractor_id);
			if (!$contractor) continue;

			// промокод данного типа контрагент может получить только единожды
			$promocode = Promocode::where('is_active', true)
				->where('contractor_id', $contractor->id)
				->where('type', Promocode::SIMULATOR_TYPE)
				->first();
			if ($promocode) continue;

			// проверяем, есть ли в данном городе локация с другим типом авиатренажера
			//\DB::connection()->enableQueryLog();
			$location = Location::where('is_active', true)
				->where('city_id', $city->id)
				->whereRelation('simulators', 'flight_simulators.id', '!=', $simulator->id)
				->first();
			//\Log::debug(\DB::getQueryLog());
			if (!$location) continue;

			$anotherSimulator = FlightSimulator::where('is_active', true)
				->where('id', '!=', $simulator->id)
				->first();
			if (!$anotherSimulator) continue;

			$discount = HelpFunctions::getEntityByAlias(Discount::class, 'fixed_1000_RUB');
			if (!$discount) continue;
		
			try {
				$promocode = new Promocode();
				$promocode->number = $city->alias . $location->alias . $anotherSimulator->alias . rand(100000, 999999);
				$promocode->type = Promocode::SIMULATOR_TYPE;
				$promocode->contractor_id = $contractor->id;
				$promocode->location_id = $location->id;
				$promocode->flight_simulator_id = $anotherSimulator->id;
				$promocode->discount_id = $discount->id;
				$promocode->save();
				
				$promocode->cities()->sync((array)$city->id);
				
				// отправим в мобилку уведомление о промокоде тоже
				$notification = new Notification();
				$notification->title = 'Дарим скидку ' . ($promocode->discount->valueFormatted() ?? '') . ' по персональному промокоду';
				$notification->description = 'Воспользуйтесь промокодом ' . $promocode->number . ' и получите скидку ' . ($promocode->discount->valueFormatted() ?? '') . ' на полет в Dream Aero на авиатренажере ' . $anotherSimulator->name . ' по адресу ' . (array_key_exists('address', $location->data_json) ? $location->data_json['address'] : '') . '.';
				$notification->city_id = $city->id;
				$notification->contractor_id = $contractor->id;
				$notification->is_active = true;
				$notification->save();
			
				$notification->contractors()->attach($contractor->id);
			
				//dispatch(new \App\Jobs\SendPromocodeAfterFlightEmail($contractor, $location, $anotherSimulator, $deal, $promocode));
				$job = new \App\Jobs\SendPromocodeAfterFlightEmail($contractor, $location, $anotherSimulator, $deal, $promocode);
				$job->handle();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
				
				return 0;
			}
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - promocode_email:send - OK');
    	
        return 0;
    }
}
