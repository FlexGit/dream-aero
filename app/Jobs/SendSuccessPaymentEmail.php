<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Bill;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Mail;

class SendSuccessPaymentEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $certificate;
	protected $bill;

	public function __construct(Bill $bill, Certificate $certificate = null) {
		$this->certificate = $certificate;
		$this->bill = $bill;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		\Log::debug('success payment email: 1');
		$contractor = $this->bill->contractor;
		if (!$contractor) return null;
		
		\Log::debug('success payment email: 2');
		$deal = $this->bill->deal;
		if (!$deal) return null;
		
		\Log::debug('success payment email: 3');
		$position = $this->bill->position;
		if (!$position) return null;
		
		\Log::debug('success payment email: 4');
		$contractor = $deal->contractor;
		if (!$contractor) return null;
		
		\Log::debug('success payment email: 5');
		$city = $contractor->city;
		if (!$city) return null;
		\Log::debug('success payment email: 6');
		if (!$city->email) return null;
		\Log::debug('success payment email: 7');
		
		$location = $this->bill->location ?? null;
		$certificate = $this->certificate ?? null;
		
		$messageData = [
			'bill' => $this->bill,
			'certificate' => $certificate,
			'contractor' => $contractor,
			'deal' => $deal,
			'position' => $position,
			'location' => $location,
		];
		
		$recipients = $bcc = [];
		$recipients[] = $city->email;
		$bcc[] = env('DEV_EMAIL');
		
		$subject = env('APP_NAME') . ': оплата Счета ' . $this->bill->number;
		
		Mail::send(['html' => "admin.emails.send_success_payment"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
			$message->bcc($bcc);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return null;
		}
		
		$this->bill->success_payment_sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->bill->save();
	}
}
