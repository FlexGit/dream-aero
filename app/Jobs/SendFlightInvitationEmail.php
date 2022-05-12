<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Bill;
use App\Models\City;
use App\Models\Event;
use App\Repositories\CityRepository;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Mail;

class SendFlightInvitationEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $event;

	public function __construct(Event $event) {
		$this->event = $event;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$flightInvitationFilePath = $this->event->data_json['flight_invitation_file_path'] ?? '';
		$flightInvitationFileExists = Storage::disk('private')->exists($flightInvitationFilePath);
		if (!isset($flightInvitationFilePath) || !$flightInvitationFileExists) {
			$this->event = $this->event->generateFile();
			if (!$this->event) {
				return null;
			}
		}
		\Log::debug(1);
		
		$city = $this->event->city;
		if (!$city) return null;
		\Log::debug(2);

		$contractor = $this->event->contractor;
		if (!$contractor) return null;
		\Log::debug(3);
		
		$deal = $this->event->deal;
		if (!$deal) return null;
		\Log::debug(4);
		
		$location = $this->event->location;
		if (!$location) return null;
		\Log::debug(5);
		
		$simulator = $this->event->simulator;
		if (!$simulator) return null;
		\Log::debug(6);
		
		$position = $this->event->dealPosition;
		if (!$position) return null;
		\Log::debug(7);
		
		$bill = $position->bill;
		if ($bill) {
			// если к позиции привязан счет, то он должен быть оплачен
			if ($bill->status->alias != Bill::PAYED_STATUS) {
				return null;
			}
			$amount = $bill->amount;
			if ($amount <= 0) return null;
		} else {
			// если к позиции не привязан счет, то проверяем чтобы вся сделка была оплачена
			$balance = $deal->balance();
			if ($balance < 0) return null;
		}
		\Log::debug(8);
		
		$dealEmail = $deal->email ?? '';
		$dealName = $deal->name ?? '';
		$contractorEmail = $contractor->email ?? '';
		$contractorName = $contractor->name ?? '';
		if (!$dealEmail && !$contractorEmail) {
			return null;
		}
		\Log::debug(10);
		
		$simulatorAlias = $simulator->alias ?? '';
		if (!$simulatorAlias) return null;
		\Log::debug(11);
		
		$messageData = [
			'name' => $dealName ?: $contractorName,
			'flightDate' => $this->event->start_at ?? '',
			'location' => $location,
			'simulator' => $simulator,
			'amount' => $amount,
			'bill' => $bill,
			'city' => $city,
			'payLink' => (($city->version == City::EN_VERSION) ? url('//' . env('DOMAIN_EN')) : url('//' . env('DOMAIN_RU'))) . '/payment/' . $bill->uuid,
		];
		
		$recipients = [];
		$recipients[] = $dealEmail ?: $contractorEmail;
		
		$subject = env('APP_NAME') . ': приглашение на полет';

		Mail::send(['html' => "admin.emails.send_flight_invitation"], $messageData, function ($message) use ($subject, $recipients, $flightInvitationFilePath) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->attach(Storage::disk('private')->path($flightInvitationFilePath));
			$message->attach(Storage::disk('private')->path('rule/RULES_MAIN.jpg'));
			$message->to($recipients);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return null;
		}
		
		$this->event->flight_invitation_sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->event->save();
	}
}
