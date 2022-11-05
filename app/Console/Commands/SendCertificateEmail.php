<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\Contractor;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SendCertificateEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificate_email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor with Certificate';

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
    	$certificates = Certificate::whereNull('sent_at')
			->where(function ($query) {
				$query->whereNull('expire_at')
					->orWhere('expire_at', '>=', Carbon::now()->format('Y-m-d H:i:s'));
			})
			->whereHas('position', function ($query) {
				$query->where('is_certificate_purchase', true)
					->whereHas('deal', function ($query) {
						$query->where('email', '!=', Contractor::ANONYM_EMAIL);
					})
					->whereRelation('bills', function ($query) {
						$query->whereRelation('paymentMethod', 'payment_methods.alias', '=', PaymentMethod::ONLINE_ALIAS)
							->whereRelation('status', 'statuses.alias', '=', Bill::PAYED_STATUS)
							->whereBetween('payed_at', [Carbon::now()->subMinutes(30)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')]);
					});
			})
			->limit(50)
			->get();
    	//\Log::debug(\DB::getQueryLog());
    	/** @var Certificate[] $certificates */
		foreach ($certificates as $certificate) {
			if ($certificate->sent_at) continue;
			
			/** @var DealPosition $position */
			$position = $certificate->position;
			if (!$position) continue;
			
			$balance = $position->balance();
			if ($balance < 0) continue;
			
			$isOnlinePaid = false;
			foreach ($position->bills as $bill) {
				if ($bill->paymentMethod && $bill->paymentMethod->alias == PaymentMethod::ONLINE_ALIAS) {
					$isOnlinePaid = true;
				}
			}
			if (!$isOnlinePaid) continue;
			
			/** @var Deal $deal */
			$deal = $position->deal;
			if (!$deal) continue;
		
			try {
				$job = new \App\Jobs\SendCertificateEmail($certificate);
				$job->handle();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
			
				return 0;
			}
		}
	
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - ' . get_class($this) . ': OK');
    	
        return 0;
    }
}
