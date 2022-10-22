<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Deal;
use App\Services\AeroflotBonusService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunAeroflotAccrual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aeroflot_accrual:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Aeroflot Bonus miles accrual';

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
    	//\DB::connection()->enableQueryLog();
    	$bills = Bill::where('aeroflot_transaction_type', AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS)
			->whereNotNull('aeroflot_card_number')
			->where('aeroflot_bonus_amount', '>', 0)
			->whereNull('aeroflot_status')
			->whereNull('aeroflot_state')
			->whereNotNull('payed_at')
			->has('status')
			->oldest()
			->get();
    	//\Log::debug(\DB::getQueryLog());
    	/** @var Bill[] $bills */
		foreach ($bills as $bill) {
			if (!in_array($bill->status->alias, [Bill::PAYED_STATUS])) continue;

			$deal = $bill->deal;
			if (!$deal || !$deal->status) continue;
			if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) continue;
			
			$position = $bill->positions()->oldest()->first();
			if (!$position) continue;
			
			// с момента оплаты должно пройти не менее заданного кол-ва дней
			// (разного для бронирования и покупки сертификата)
			//\Log::debug($position->is_certificate_purchase . ' - ' . $bill->payed_at->format('Y-m-d H:i:s') . ' - ' . Carbon::now()->format('Y-m-d H:i:s'));
			if ((!$position->is_certificate_purchase && Carbon::parse($bill->payed_at)->addDays(AeroflotBonusService::BOOKING_ACCRUAL_AFTER_DAYS)->gt(Carbon::now()))
				|| ($position->is_certificate_purchase && Carbon::parse($bill->payed_at)->addDays(AeroflotBonusService::CERTIFICATE_PURCHASE_ACCRUAL_AFTER_DAYS)->gt(Carbon::now()))
			) {
				continue;
			}
			
			$result = AeroflotBonusService::authPoints($bill);
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - aeroflot_accrual:run - OK');
    	
        return 0;
    }
}
