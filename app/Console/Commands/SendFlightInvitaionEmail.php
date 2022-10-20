<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SendFlightInvitationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flight_invitation_email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor with flight invitation';

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
			->whereNull('flight_invitation_sent_at')
			->whereNull('simulator_up_at')
			->where('parent_id', 0)
			->whereRelation('dealPosition', function ($query) {
				$query->whereRelation('bills', function ($query) {
					$query->whereRelation('paymentMethod', 'payment_methods.alias', '=', PaymentMethod::ONLINE_ALIAS)
						->whereRelation('status', 'statuses.alias', '=', Bill::PAYED_STATUS);
				});
			})
			->latest()
			->limit(100)
			->get();
    	/** @var Event[] $events */
		foreach ($events as $event) {
			if (!$event->uuid) continue;
			
			/** @var DealPosition $position */
			/*$position = $event->dealPosition;
			if (!$position) continue;*/
			
			if (!$position->is_certificate_purchase && $position->certificate) continue;
		
			/** @var Deal $deal */
			/*$deal = $position->deal;
			if (!$deal) continue;*/
		
			/*$isOnlinePaymentMethodExist = false;
			foreach ($position->bills as $bill) {
				if ($bill->status && $bill->status->alias == Bill::PAYED_STATUS && $bill->paymentMethod && $bill->paymentMethod->alias = PaymentMethod::ONLINE_ALIAS) {
					$isOnlinePaymentMethodExist = true;
					break;
				}
			}
			if (!$isOnlinePaymentMethodExist) continue;*/
			
			$balance = $position->balance();
			if ($balance < 0) continue;
   
			try {
				$job = new \App\Jobs\SendFlightInvitationEmail($event);
				$job->handle();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
			
				return 0;
			}
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - flight_invitation_email:send - OK');
    	
        return 0;
    }
}
