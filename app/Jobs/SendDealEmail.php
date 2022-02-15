<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
/*use App\Services\Locker;
use App\Services\Semaphore;*/
use App\Models\Deal;
use App\Models\Score;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendDealEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;
	
	protected $dealId;
	
	public function __construct(Deal $deal) {
		$this->dealId = $deal->id;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		/*$semaphore = Semaphore::getSemaphoreOrNull('SendDealEmail', 1, 0, 50); // 1 слот для одновременной обработки, 0 секунд ждём свою очередь, 50 секунд даём на обработку
		if (!$semaphore) {
			// пишем в лог проблему на случай, если попыток было слишком много и всё равно требуется начинать job с нуля
			if ($this->attempts() >= 5) {
				\Log::error("Can't execute SendDealEmail for {$this->dealId} -- no semaphore slots left at least 5 times in a row, RELEASED ONCE MORE as a clean copy");
				return $this->releaseCleanAttempt(5);
			} else {
				return $this->releaseAgain($this->attempts() * 3);
			}
		}*/
		
		$deal = Deal::find($this->dealId);
		if (!$deal) return;
		
		/*$lock = Locker::getLockOrNull("SendDealEmail_{$deal->id}", 15, 100); // 15 секунд ждём лок, 100 секунд даём на обработку
		if (!$lock) {
			if ($this->attempts() <= 4) {
				$this->releaseAgain(10);
			} else {
				$this->releaseCleanAttempt(10);
			}
			return;
		}*/
		
		// логируем рассылку
		/*$log = new \Monolog\Logger('deal_send_notifier');
		try {
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path('logs/deal_send_notifier.log'), \Monolog\Logger::INFO));
		} catch (\Exception $e) {
			\Log::critical('Cannot create log file for deal_send_notifier', [$e->getMessage()]);
			$log = \Log::getMonolog();
		}*/

		$position = $deal->positions()->first();
		if (!$position) return;

		$score = 0;
		foreach ($deal->positions ?? [] as $position) {
			if (!$position->score || !$position->type != Score::USED_TYPE) continue;

			$score += $position->score->score;
		}

		$locationData = $position->location ? $position->location->data_json ?? [] : [];

		if ($deal->contractor && $deal->contractor->city) {
			$cityData = [
				'phone' => $deal->contractor->city->phone ?? '',
				'email' => $deal->contractor->city->email ?? '',
			];
		}
		if (!$cityData['email']) return;

		$recipients = [];
		$recipients[] = $cityData['email'];
		
		$messageData = [
			'name' => $position->name,
			'number' => $position->number,
			'isCertificatePurchase' => (bool)$position->is_certificate_purchase,
			'statusName' => $deal->status ? $deal->status->name : '',
			'certificateNumber' => $position->certificate ? $position->certificate->number : '',
			'certificateExpireAt' => $position->certificate ? $position->certificate->expire_at : '',
			'flightAt' => $position->flight_at,
			'cityName' => $position->city ? $position->city->name : '',
			'locationAddress' => array_key_exists('address', $locationData) ? $locationData['address'] : '',
			'productName' => $position->product ? $position->product->name : '',
			'duration' => $position->duration,
			'amount' => $position->amount,
			'score' => $score,
			'phone' => array_key_exists('phone', $locationData) ? $locationData['phone'] : $cityData['phone'],
			'whatsapp' => array_key_exists('whatsapp', $locationData) ? $locationData['whatsapp'] : '',
			'skype' => array_key_exists('skype', $locationData) ? $locationData['skype'] : '',
			'email' => array_key_exists('email', $locationData) ? $locationData['email'] : $cityData['email'],
		];
		
		try {
			$subject = $position->is_certificate_purchase ? env('APP_NAME') . ': заявка на покупку сертификата' : env('APP_NAME') . ': заявка на бронирование полета';
			Mail::send(['html' => "admin.emails.send_deal"], $messageData, function ($message) use ($subject, $deal, $recipients) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				$message->priority(2);
				$message->to($recipients); //$deal->email
			});
			foreach ($recipients as $recipient) {
				if (in_array($recipient, Mail::failures())) {
					throw new \Exception("Email $recipient in a failed list");
				}
			}
		} catch (\Exception $e) {
			/*$log->error('ERROR on Deal send ', ['number' => $deal->number, 'email' => $deal->email, 'msg' => $e->getMessage()]);*/
		}
	}
}
