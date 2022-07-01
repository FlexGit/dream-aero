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

class SendReviewAfterFlightEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'review_email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor asking for feedback after a flight';

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
    	// проверяем все полеты с начала дня до текущего момента
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			/*->whereBetween('stop_at', [Carbon::now()->subDays(2), Carbon::now()->subDays(1)])*/
			->where('id', 26519)
			->whereNull('feedback_email_sent_at')
			->oldest()
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

			try {
				$job = new \App\Jobs\SendReviewAfterFlightEmail($event, $contractor, $location, $simulator, $deal);
				$job->handle();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
				
				return 0;
			}
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - review_email:send - OK');
    	
        return 0;
    }
}
