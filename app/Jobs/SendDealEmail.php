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
	
	protected $deal;
	
	public function __construct(Deal $deal) {
		$this->deal = $deal;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$position = $this->deal->positions()->first();
		if (!$position) return;

		$scoreAmount = 0;
		if ($this->deal->scores) {
			foreach ($this->deal->scores ?? [] as $score) {
				if ($score->type != Score::USED_TYPE) continue;

				$scoreAmount += abs($score->score);
			}
		}
		
		$positionData = !is_array($position->data_json) ? json_decode($position->data_json, true) : $position->data_json;
		$locationData = $position->location ? $position->location->data_json ?? [] : [];

		if ($this->deal->contractor && $this->deal->contractor->city) {
			$cityData = [
				'phone' => $this->deal->contractor->city->phone ?? '',
				'email' => $this->deal->contractor->city->email ?? '',
			];
		}

		$recipients = [];
		if ($cityData['email']) {
			$recipients[] = $cityData['email'];
		}
		$recipients[] = env('DEV_EMAIL');

		$messageData = [
			'contractorFio' => $this->deal->contractor ? $this->deal->contractor->fio() : '',
			'dealName' => $this->deal->name ?? '',
			'dealPhone' => $this->deal->phone ?? '',
			'dealEmail' => $this->deal->email ?? '',
			'dealNumber' => $this->deal->number ?? '',
			'positionNumber' => $position->number ?? '',
			'isCertificatePurchase' => (bool)$position->is_certificate_purchase,
			'statusName' => $this->deal->status ? $this->deal->status->name : '',
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
			'updatedAt' => $this->deal->updated_at,
			'productName' => $position->product ? $position->product->name : '',
			'duration' => $position->duration,
			'amount' => $position->amount,
			'currency' => $position->currency ? $position->currency->name : '',
			'scoreAmount' => $scoreAmount ?? 0,
			'phone' => array_key_exists('phone', $locationData) ? $locationData['phone'] : $cityData['phone'],
			'whatsapp' => array_key_exists('whatsapp', $locationData) ? $locationData['whatsapp'] : '',
			'skype' => array_key_exists('skype', $locationData) ? $locationData['skype'] : '',
			'email' => array_key_exists('email', $locationData) ? $locationData['email'] : $cityData['email'],
			/*'comment' => ((array_key_exists('comment', $positionData) && $positionData['comment']) ? $positionData['comment'] : '') . ((array_key_exists('certificate_whom', $positionData) && $positionData['certificate_whom']) ? '. Сертификат для: ' . $positionData['certificate_whom'] : ''),*/
		];

		$subject = $position->is_certificate_purchase ? env('APP_NAME') . ': заявка на покупку сертификата' : env('APP_NAME') . ': заявка на бронирование полета';

		// контрагенту
		/*if ($this->deal->email) {
			Mail::send(['html' => "admin.emails.send_deal"], $messageData, function ($message) use ($subject) {
				/** @var \Illuminate\Mail\Message $message */
				/*$message->subject($subject);
				$message->to($this->deal->email);
			});
		}*/

		// админу
		if ($recipients) {
			Mail::send(['html' => "admin.emails.send_deal_admin"], $messageData, function ($message) use ($subject, $recipients) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				$message->to($recipients);
			});
		}
	}
}
