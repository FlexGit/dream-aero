<?php

namespace App\Console\Commands;

use App\Models\Deal;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SetDealCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal_city:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Deal City if empty';

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
    	$deals = Deal::where('city_id', 0)
			->get();
    	/** @var Deal[] $deals */
		foreach ($deals as $deal) {
			$position = $deal->positions->where('city_id', '!=', 0)->first();
			if (!$position) continue;
			
			/*$contractor = $deal->contractor;
			if (!$contractor) continue;*/

    		$city = $position->city;
    		if (!$city) continue;
			
			$deal->city_id = $city->id;
			$deal->save();
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - deal_city:set - OK');
    	
        return 0;
    }
}
