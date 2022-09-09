<?php

namespace App\Console\Commands;

use App\Models\DealPosition;
use App\Models\Event;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\HelpFunctions;
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
    	$events = Event::where('nominal_price', 0)
			->get();
		foreach ($events as $event) {
			/** @var DealPosition $position */
			$position = $event->dealPosition;
			if (!$position) continue;
			
			/** @var Product $product */
			$product = $position->product;
			if (!$product) continue;
			
			/** @var ProductType $productType */
			$productType = $product->productType;
			
			// если тариф Ultimate, а летали в будний день, то меняем тариф на Regular
			if ($productType->alias == ProductType::ULTIMATE_ALIAS && in_array(Carbon::parse($event->start_at)->dayOfWeek, [0,6])) {
				$product = HelpFunctions::getEntityByAlias(Product::class, ProductType::REGULAR_ALIAS);
			}

			$cityId = $event->city_id;
			$cityProduct = $product->cities()->find($cityId);
			if (!$cityProduct || !$cityProduct->pivot) continue;
			
			$event->nominal_price = $cityProduct->pivot->price ?? 0;
			$event->save();
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - nominal_price:set - OK');
    	
        return 0;
    }
}
