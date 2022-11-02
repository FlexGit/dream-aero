<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Bill;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Event;
use App\Models\Task;
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
		$flightInvitationFileExists = Storage::disk('private')->exists($flightInvitationFilePath);
		
		// если файла приглашения по какой-то причине не оказалось, генерим его
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
		
		if (!$deal->name || !$deal->email || $deal->email == Contractor::ANONYM_EMAIL) {
			return null;
		}
		
		$simulatorAlias = $simulator->alias ?? '';
		if (!$simulatorAlias) return null;
		
		$messageData = [
			'name' => $deal->name,
			'flightDate' => $this->event->start_at ? Carbon::parse($this->event->start_at)->format('d.m.Y H:i') : '',
			'location' => $location,
			'simulator' => $simulator,
			'city' => $city,
		];
		
		$recipients = $bcc = [];
		$recipients[] = $deal->email;
		if ($city->email) {
			$bcc[] = $city->email;
		}
		
		$subject = env('APP_NAME') . ': приглашение на полет';

		Mail::send(['html' => "admin.emails.send_flight_invitation"], $messageData, function ($message) use ($subject, $recipients, $flightInvitationFilePath, $bcc) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->attach(Storage::disk('private')->path($this->event->data_json['flight_invitation_file_path']));
			$message->to($recipients);
			$message->bcc($bcc);
		});
		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - ' . get_class($this) . ': ' . $this->event->id . ' - ' . $this->event->data_json['flight_invitation_file_path']);
			return null;
		}
		
		$this->event->flight_invitation_sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->event->save();
		
		$task = new Task();
		$task->name = get_class($this);
		$task->email = implode(',', array_merge($recipients, $bcc));
		$task->object_uuid = $this->event->uuid;
		$task->save();
	}
}
