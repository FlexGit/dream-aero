<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Deal;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendReviewAfterFlightEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;
	
	protected $event;
	protected $contractor;
	protected $location;
	protected $simulator;
	protected $deal;

	public function __construct(Event $event, Contractor $contractor, Location $location, FlightSimulator $simulator, Deal $deal) {
		$this->event = $event;
		$this->contractor = $contractor;
		$this->location = $location;
		$this->simulator = $simulator;
		$this->deal = $deal;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = $bcc = [];
		$recipients[] = $this->deal->email;
		$bcc[] = env('DEV_EMAIL');

		$messageData = [
			'event' => $this->event ?: new Event(),
			'contractor' => $this->contractor ?: new Contractor(),
			'location' => $this->location ?: new Location(),
			'simulator' => $this->simulator ?: new FlightSimulator(),
			'deal' => $this->deal ?: new Deal(),
			'city' => $this->deal->city ?: new City(),
		];

		$subject = env('APP_NAME') . ': оставьте отзыв';

		Mail::send(['html' => "admin.emails.send_review_after_flight"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
			$message->bcc($bcc);
		});
		$failures = Mail::failures();
		if (!$failures) {
			$this->event->feedback_email_sent_at = Carbon::now()->format('Y-m-d H:i:s');
			$this->event->save();
		}
	}
}
