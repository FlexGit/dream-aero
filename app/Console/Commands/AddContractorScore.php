<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Score;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddContractorScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'score:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add scores to a contractor after a flight';

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
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->whereBetween('stop_at', [Carbon::now()->subDays(5), Carbon::now()])
			->where('contractor_id', '!=', '78800') // ToDo не начислять баллы Анониму (заменить на что-то более красивое)
			->get();
    	foreach ($events as $event) {
    		if ($event->id == 18992) {
    			\Log::debug($event->id . ': 1');
			}
    		$score = Score::where('event_id', $event->id)->first();
    		if ($score) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 2');
			}

    		$position = $event->dealPosition;
			if (!$position) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 3');
			}
		
			$product = $position->product;
			if (!$product) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 4');
			}
		
			$city = $position->city;
			if (!$city) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 5');
			}
		
			$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
			if (!$cityProduct) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 6');
			}
			if (!$cityProduct->pivot) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 7');
			}
			if ($cityProduct->pivot->score <= 0) continue;
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 8');
			}

    		$score = new Score();
    		$score->score = $cityProduct->pivot->score;
			$score->contractor_id = $event->contractor_id;
			$score->deal_id = $event->deal_id;
			$score->deal_position_id = $event->deal_position_id;
			$score->event_id = $event->id;
			$score->duration = $product->duration;
			$score->type = Score::SCORING_TYPE;
			$score->save();
			if ($event->id == 18992) {
				\Log::debug($event->id . ': 9');
			}
		
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - score:add - OK');
    	
        return 0;
    }
}
