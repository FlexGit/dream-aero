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
		$flightInvitationFilePath = isset($this->event->data_json['flight_invitation_file_path']) ? $this->event->data_json['flight_invitation_file_path'] : '';
		if ($flightInvitationFilePath) {
			$flightInvitationFileExists = Storage::disk('private')->exists($flightInvitationFilePath);
		}
		if (!$flightInvitationFilePath || !$flightInvitationFileExists) {
			$this->event = $this->event->generateFile();
			if (!$this->event) {
				return null;
			}
		}
		
		$city = $this->event->city;
		if (!$city) return null;

		$contractor = $this->event->contractor;
		if (!$contractor) return null;
		
		$deal = $this->event->deal;
		if (!$deal) return null;
		
		$location = $this->event->location;
		if (!$location) return null;
		
		$simulator = $this->event->simulator;
		if (!$simulator) return null;
		
		$position = $this->event->dealPosition;
		if (!$position) return null;
		
		//$amount = 0;
		$bill = $position->bill;
		/*if ($bill) {
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
		}*/
		
		$dealEmail = $deal->email ?? '';
		$dealName = $deal->name ?? '';
		$contractorEmail = $contractor->email ?? '';
		$contractorName = $contractor->name ?? '';
		if (!$dealEmail && !$contractorEmail) {
			return null;
		}
		
		$simulatorAlias = $simulator->alias ?? '';
		if (!$simulatorAlias) return null;
		
		$messageData = [
			'name' => $dealName ?: $contractorName,
			'flightDate' => $this->event->start_at ?? '',
			'location' => $location,
			'simulator' => $simulator,
			'amount' => $bill->amount ?? 0,
			'city' => $city,
			'payLink' => $bill ? ((($city->version == City::EN_VERSION) ? url('//' . env('DOMAIN_EN')) : url('//' . env('DOMAIN_RU'))) . '/payment/' . $bill->uuid) : '',
		];
		
		$recipients = [];
		$recipients[] = $dealEmail ?: $contractorEmail;
		
		$subject = env('APP_NAME') . ': приглашение на полет';

		Mail::send(['html' => "admin.emails.send_flight_invitation"], $messageData, function ($message) use ($subject, $recipients, $flightInvitationFilePath) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->attach(Storage::disk('private')->path($flightInvitationFilePath));
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
