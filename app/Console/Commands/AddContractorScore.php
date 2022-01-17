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
			->where('stop_at', '<', Carbon::now()->addHour()->format('Y-m-d H:i:s'))
			->with('deal')
			->get();
    	foreach ($events as $event) {
    		$score = Score::where('event_id', $event->id)->first();
    		if ($score) continue;
    		
    		$score = new Score();
    		$score->score = Carbon::parse($event->stop_at)->diffInMinutes(Carbon::parse($event->start_at));
			$score->contractor_id = $event->deal ? $event->deal->contractor_id : 0;
			$score->event_id = $event->id;
			$score->save();
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - score:add - OK');
    	
        return 0;
    }
}
