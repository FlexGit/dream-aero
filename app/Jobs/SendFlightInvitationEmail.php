<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Event;
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
		
		$simulatorAlias = $this->event->simulator->alias ?? '';
		if (!$simulatorAlias) return null;
		
		$dealEmail = $this->event->deal->email ?? '';
		$dealName = $this->event->deal->name ?? '';
		$contractorEmail = $this->event->contractor->email ?? '';
		$contractorName = $this->event->contractor->name ?? '';
		if (!$dealEmail && !$contractorEmail) {
			return null;
		}
		
		$messageData = [
			'name' => $dealName ?: $contractorName,
			'flightDate' => $this->event->start_at ?? '',
			'locationName' => $this->event->location->name ?? '',
			'simulatorName' => $this->event->simulator->name ?? '',
			'amount' => $this->event->dealPosition->amount ?? '',
			'billUuid' => $this->event->dealPosition->bill->uuid ?? '',
			'cityEmail' => $this->event->city->email ?? '',
			'cityPhone' => $this->event->city->phone ?? '',
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
