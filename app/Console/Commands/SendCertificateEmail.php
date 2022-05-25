<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\Deal;
use App\Models\DealPosition;
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
    	$certificates = Certificate::whereNull('sent_at')
			->get();
    	/** @var Certificate[] $certificates */
		foreach ($certificates as $certificate) {
			/** @var DealPosition $position */
			$position = $certificate->position;
			if (!$position) continue;
		
			/** @var Deal $deal */
			$deal = $position->deal;
			if (!$deal) continue;
		
			/** @var Bill $bill */
			$bill = $position->bill;
			if ($bill) {
				$status = $bill->status;
				if (!$status) continue;
				
				// если к позиции привязан счет, то он должен быть оплачен
				if ($status->alias != Bill::PAYED_STATUS) continue;
			} else {
				// если к позиции не привязан счет, то проверяем чтобы вся сделка была оплачена
				$balance = $deal->balance();
				if ($balance < 0) continue;
			}
   
			try {
				//dispatch(new \App\Jobs\SendCertificateEmail($certificate));
				$job = new \App\Jobs\SendCertificateEmail($certificate);
				$job->handle();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
			
				return 0;
			}
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - certificate_email:send - OK');
    	
        return 0;
    }
}
