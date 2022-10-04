<?php

namespace App\Console\Commands;

use App\Models\DealPosition;
use App\Models\Event;
use App\Models\Product;
use App\Models\ProductType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetNominalPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nominal_price:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Nominal Price';

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
		//\DB::connection()->enableQueryLog();
    	$events = Event::whereIn('event_type', [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_USER_FLIGHT])
			->where('nominal_price', 0)
			->orderBy('id')
			->get();
		//\Log::debug(\DB::getQueryLog());
		foreach ($events as $event) {
			/** @var Event $event */
			if ($event->event_type == Event::EVENT_TYPE_DEAL) {
				$position = $event->dealPosition;
				if (!$position) continue;
				
				/** @var Product $product */
				$product = $position->product;
				if (!$product) continue;
				
				/** @var ProductType $productType */
				$productType = $product->productType;
				if (!$productType) continue;
			}
			
			$event->nominal_price = $event->nominalPrice();
			$event->save();
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - nominal_price:set - OK');
    	
        return 0;
    }
}
