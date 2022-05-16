<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SetPilotAfterFlight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set pilot after flight complete';

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
    	// проверяем все полеты за последний час без пилота
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->where('stop_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->where('stop_at', '>=', Carbon::now()->subHour()->format('Y-m-d H:i:s'))
			->where('pilot_id', 0)
			->get();
    	/** @var Event[] $events */
		foreach ($events as $event) {
			$city = $event->city;
			if (!$city) continue;

    		$location = $event->location;
    		if (!$location) continue;
		
			$simulator = $event->simulator;
			if (!$simulator) continue;
		
			// находим пилота, который был на смене во время полета
			$shiftEvent = Event::where('event_type', Event::EVENT_TYPE_SHIFT_PILOT)
				->where('user_id', '!=', 0)
				->where('city_id', $city->id)
				->where('location_id', $location->id)
				->where('flight_simulator_id', $simulator->id)
				->whereDate('start_at', '>=', $event->start_at)
				->whereDate('stop_at', '<=', $event->stop_at)
				->first();
			if (!$shiftEvent) continue;
			
			$event->pilot_id = $shiftEvent->user_id;
			$event->save();
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - pilot:set - OK');
    	
        return 0;
    }
}
