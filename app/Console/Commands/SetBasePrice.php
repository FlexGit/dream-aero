<?php

namespace App\Console\Commands;

use App\Models\DealPosition;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetBasePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base_price:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Base Price';

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
    	$positions = DealPosition::where('price', 0)
			->get();
		foreach ($positions as $position) {
			/** @var Product $product */
			$product = $position->product;
			if (!$product) continue;

			$cityId = $position->city_id ?: 1;
			$cityProduct = $product->cities()->find($cityId);
			if (!$cityProduct || !$cityProduct->pivot) continue;
			
			$position->price = $cityProduct->pivot->price ?? 0;
			$position->save();
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - base_price:set - OK');
    	
        return 0;
    }
}
