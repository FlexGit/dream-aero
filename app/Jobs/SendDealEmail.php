<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
/*use App\Services\Locker;
use App\Services\Semaphore;*/
use App\Models\Deal;
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

		$locationData = $position->location ? $position->location->data_json ?? [] : [];
		
		// собираем контакты со всех локаций города на случай, если локации в заказе не было
		// либо нет данных о конкретном виде связи
		$cityData = [
			'phone' => [],
			'whatsapp' => [],
			'skype' => [],
			'email' => [],
		];
		if ($position->city && $position->city->locations) {
			foreach ($position->city->locations as $location) {
				$locationData = $location->data_json ?? [];
				if (array_key_exists('phone', $locationData)) {
					$cityData['phone'][] = $locationData['phone'];
				}
				if (array_key_exists('whatsapp', $locationData)) {
					$cityData['whatsapp'][] = $locationData['whatsapp'];
				}
				if (array_key_exists('skype', $locationData)) {
					$cityData['skype'][] = $locationData['skype'];
				}
				if (array_key_exists('email', $locationData)) {
					$cityData['email'][] = $locationData['email'];
				}
			}
		}
		
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
			'phone' => array_key_exists('phone', $locationData) ? $locationData['phone'] : implode(', ', $cityData['phone']),
			'whatsapp' => array_key_exists('whatsapp', $locationData) ? $locationData['whatsapp'] : implode(', ', $cityData['whatsapp']),
			'skype' => array_key_exists('skype', $locationData) ? $locationData['skype'] : implode(', ', $cityData['skype']),
			'email' => array_key_exists('email', $locationData) ? $locationData['email'] : implode(', ', $cityData['email']),
		];
		
		try {
			$subject = $position->is_certificate_purchase ? env('APP_NAME') . ': заявка на покупку сертификата' : env('APP_NAME') . ': заявка на бронирование полета';
			Mail::send(['html' => "admin.emails.send_deal"], $messageData, function ($message) use ($subject, $deal) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				$message->priority(2);
				$message->to($deal->email);
			});
			if (in_array($deal->email, Mail::failures())) {
				throw new \Exception("Email $deal->email in a failed list");
			}
		} catch (\Exception $e) {
			/*$log->error('ERROR on Deal send ', ['number' => $deal->number, 'email' => $deal->email, 'msg' => $e->getMessage()]);*/
		}
	}
}
