<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SetAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set admin temp';

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
		\DB::connection()->enableQueryLog();
    	// проверяем все полеты за последний час без пилота
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->where('stop_at', '<=', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'))
			->where('stop_at', '>=', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'))
			->get();
    	/** @var Event[] $events */
		foreach ($events as $event) {
			$city = $event->city;
			if (!$city) continue;

    		$location = $event->location;
    		if (!$location) continue;
		
			$simulator = $event->simulator;
			if (!$simulator) continue;
		
			// находим админа, который был на смене во время полета
			$shiftEvent = Event::where('event_type', Event::EVENT_TYPE_SHIFT_ADMIN)
				->where('user_id', '!=', 0)
				->where('city_id', $city->id)
				->where('location_id', $location->id)
				->where('flight_simulator_id', $simulator->id)
				->where('start_at', '<=', Carbon::parse($event->start_at)->format('Y-m-d H:i:s'))
				->where('stop_at', '>=', Carbon::parse($event->stop_at)->format('Y-m-d H:i:s'))
				->first();
			if (!$shiftEvent) continue;
			
			$event->temp_user_id = $shiftEvent->user_id;
			$event->save();
		}
		\Log::debug(\DB::getQueryLog());
		
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - admin:set - OK');
    	
        return 0;
    }
}
