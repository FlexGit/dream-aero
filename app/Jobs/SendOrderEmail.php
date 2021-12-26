<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Services\Locker;
use App\Services\Semaphore;
use App\Models\Order;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendOrderEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;
	
	protected $orderId;
	
	public function __construct(Order $order) {
		$this->orderId = $order->id;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$semaphore = Semaphore::getSemaphoreOrNull('SendOrderEmail', 1, 0, 50); // 1 слот для одновременной обработки, 0 секунд ждём свою очередь, 50 секунд даём на обработку
		if (!$semaphore) {
			// пишем в лог проблему на случай, если попыток было слишком много и всё равно требуется начинать job с нуля
			if ($this->attempts() >= 5) {
				\Log::error("Can't execute SendOrderEmail for {$this->orderId} -- no semaphore slots left at least 5 times in a row, RELEASED ONCE MORE as a clean copy");
				return $this->releaseCleanAttempt(5);
			} else {
				return $this->releaseAgain($this->attempts() * 3);
			}
		}
		
		$order = Order::find($this->orderId);
		if (!$order) return;
		
		$lock = Locker::getLockOrNull("SendOrderEmail_{$order->id}", 15, 100); // 15 секунд ждём лок, 100 секунд даём на обработку
		if (!$lock) {
			if ($this->attempts() <= 4) {
				$this->releaseAgain(10);
			} else {
				$this->releaseCleanAttempt(10);
			}
			return;
		}
		
		// логируем рассылку
		$log = new \Monolog\Logger('order_send_notifier');
		try {
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path('logs/order_send_notifier.log'), \Monolog\Logger::INFO));
		} catch (\Exception $e) {
			\Log::critical('Cannot create log file for inspection_notifier', [$e->getMessage()]);
			$log = \Log::getMonolog();
		}
		
		$locationData = $order->location ? $order->location->data_json ?? [] : [];
		
		// собираем контакты со всех локаций города на случай, если локации в заказе не было
		// либо нет данных о конкретном виде связи
		$cityData = [
			'phone' => [],
			'whatsapp' => [],
			'skype' => [],
			'email' => [],
		];
		if ($order->city && $order->city->locations) {
			foreach ($order->city->locations as $location) {
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
			'name' => $order->name,
			'number' => $order->number,
			'isCertificateOrder' => (bool)$order->is_certificate_order,
			'statusName' => $order->status ? $order->status->name : '',
			'certificateNumber' => $order->certificate ? $order->certificate->number : '',
			'certificateExpireAt' => $order->certificate ? $order->certificate->expire_at : '',
			'isUnified' => (bool)$order->is_unified,
			'flightAt' => $order->flight_at,
			'cityName' => $order->city ? $order->city->name : '',
			'locationAddress' => array_key_exists('address', $locationData) ? $locationData['address'] : '',
			'productName' => $order->product ? $order->product->name : '',
			'duration' => $order->duration,
			'amount' => $order->amount,
			'phone' => array_key_exists('phone', $locationData) ? $locationData['phone'] : implode(', ', $cityData['phone']),
			'whatsapp' => array_key_exists('whatsapp', $locationData) ? $locationData['whatsapp'] : implode(', ', $cityData['whatsapp']),
			'skype' => array_key_exists('skype', $locationData) ? $locationData['skype'] : implode(', ', $cityData['skype']),
			'email' => array_key_exists('email', $locationData) ? $locationData['email'] : implode(', ', $cityData['email']),
		];
		
		try {
			$subject = $order->is_certificate_order ? env('APP_NAME') . ': заявка на покупку сертификата' : env('APP_NAME') . ': заявка на бронирование полета';
			Mail::send(['html' => "admin.emails.send_order"], $messageData, function ($message) use ($subject, $order) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				$message->priority(2);
				$message->to($order->email);
			});
			if (in_array($order->email, Mail::failures())) {
				throw new \Exception("Email $order->email in a failed list");
			}
		} catch (\Exception $e) {
			$log->error('ERROR on Order send ', ['number' => $order->number, 'email' => $order->email, 'msg' => $e->getMessage()]);
		}
	}
}
