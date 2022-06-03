<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\DealPosition;
use App\Services\AeroflotBonusService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AeroflotGetOrderInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aeroflot_order_info:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Aeroflot Bonus Order info';

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
    	//\Log::debug(\DB::connection()->enableQueryLog());
    	$bills = Bill::where('aeroflot_transaction_type', AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER)
			->whereNotNull('aeroflot_transaction_order_id')
			->where('aeroflot_status', 0)
			->where(function ($query) {
				$query->where('aeroflot_state', AeroflotBonusService::REGISTERED_STATE)
					->orWhereNull('aeroflot_state');
			})
			->get();
    	//\Log::debug(\DB::getQueryLog());
    	foreach ($bills as $bill) {
			$orderInfoResult = AeroflotBonusService::getOrderInfo($bill);
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - aeroflot_order_info:get - OK');
    	
        return 0;
    }
}
