<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Bill;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendPayLinkEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $bill;

	public function __construct(Bill $bill) {
		$this->bill = $bill;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$deal = $this->bill->deal;
		if (!$deal) return null;
		
		$amount = $this->bill->amount;
		if (!$amount) return null;
		
		$contractor = $this->bill->contractor;
		if (!$contractor) return null;
		
		$city = $contractor->city;
		if (!$city) return null;
		
		$email = $deal->email ?: $contractor->email;
		if (!$email) return null;
		
		$name = $deal->name ?: $contractor->name;
		
		$messageData = [
			'name' => $name,
			'amount' => $amount,
			'payLink' => (($city->version == City::EN_VERSION) ? url('//' . env('DOMAIN_EN')) : url('//' . env('DOMAIN_RU'))) . '/payment/' . $this->bill->uuid,
			'city' => $city ?? null,
		];
		
		$recipients = [];
		$recipients[] = $email;
		
		$subject = env('APP_NAME') . ': cсылка на оплату';
		
		Mail::send('admin.emails.send_paylink', $messageData, function ($message) use ($recipients, $subject) {
			$message->subject($subject);
			$message->to($recipients);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return null;
		}
		
		$this->bill->link_sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->bill->save();
	}
}