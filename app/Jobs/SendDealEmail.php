<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Deal;
use App\Models\Score;
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
		$deal = Deal::find($this->dealId);
		if (!$deal) return;
		
		$position = $deal->positions()->first();
		if (!$position) return;

		$scoreAmount = 0;
		if ($deal->scores) {
			foreach ($deal->scores ?? [] as $score) {
				if ($score->type != Score::USED_TYPE) continue;

				$scoreAmount += abs($score->score);
			}
		}

		$locationData = $position->location ? $position->location->data_json ?? [] : [];

		if ($deal->contractor && $deal->contractor->city) {
			$cityData = [
				'phone' => $deal->contractor->city->phone ?? '',
				'email' => $deal->contractor->city->email ?? '',
			];
		}

		$recipients = [];
		//$recipients[] = 'webmanage@inbox.ru';
		if ($cityData['email']) {
			$recipients[] = $cityData['email'];
		}

		$messageData = [
			'contractorFio' => $deal->contractor ? $deal->contractor->fio() : '',
			'dealName' => $deal->name ?? '',
			'dealPhone' => $deal->phone ?? '',
			'dealEmail' => $deal->email ?? '',
			'dealNumber' => $deal->number ?? '',
			'positionNumber' => $position->number ?? '',
			'isCertificatePurchase' => (bool)$position->is_certificate_purchase,
			'statusName' => $deal->status ? $deal->status->name : '',
			'certificateNumber' => $position->certificate ? $position->certificate->number : '',
			'certificateExpireAt' => $position->certificate ? $position->certificate->expire_at : '',
			'flightAt' => $position->flight_at,
			'cityName' => $position->city ? $position->city->name : '',
			'locationName' => $position->location ? $position->location->name : '',
			'locationAddress' => array_key_exists('address', $locationData) ? $locationData['address'] : '',
			'flightSimulatorName' => $position->simulator ? $position->simulator->name : '',
			'promoName' => $position->promo ? $position->promo->name : '',
			'promocodeNumber' => $position->promocode ? $position->promocode->number : '',
			'source' => $position->source ? app('\App\Models\DealPosition')::SOURCES[$position->source] : '',
			'updatedAt' => $deal->updated_at,
			'productName' => $position->product ? $position->product->name : '',
			'duration' => $position->duration,
			'amount' => $position->amount,
			'currency' => $position->currency ? $position->currency->name : '',
			'scoreAmount' => $scoreAmount ?? 0,
			'phone' => array_key_exists('phone', $locationData) ? $locationData['phone'] : $cityData['phone'],
			'whatsapp' => array_key_exists('whatsapp', $locationData) ? $locationData['whatsapp'] : '',
			'skype' => array_key_exists('skype', $locationData) ? $locationData['skype'] : '',
			'email' => array_key_exists('email', $locationData) ? $locationData['email'] : $cityData['email'],
			'comment' => ((array_key_exists('comment', $position->data_json) && $position->data_json['comment']) ? $position->data_json['comment'] : '') . ((array_key_exists('certificate_whom', $position->data_json) && $position->data_json['certificate_whom']) ? '. Сертификат для: ' . $position->data_json['certificate_whom'] : ''),
		];

		try {
			$subject = $position->is_certificate_purchase ? env('APP_NAME') . ': заявка на покупку сертификата' : env('APP_NAME') . ': заявка на бронирование полета';

			// клиенту
			Mail::send(['html' => "admin.emails.send_deal"], $messageData, function ($message) use ($subject, $deal) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				/*$message->priority(2);*/
				$message->to($deal->email);
			});

			if (in_array($deal->email, Mail::failures())) {
				throw new \Exception("Email $deal->email in a failed list");
			}

			// админу
			Mail::send(['html' => "admin.emails.send_deal_admin"], $messageData, function ($message) use ($subject, $recipients) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				/*$message->priority(2);*/
				$message->to($recipients);
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
