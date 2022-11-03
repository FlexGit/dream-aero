<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Deal;
use App\Models\FlightSimulator;
use App\Models\Location;
use App\Models\Promocode;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendPromocodeAfterFlightEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;
	
	protected $contractor;
	protected $location;
	protected $simulator;
	protected $deal;
	protected $promocode;

	public function __construct(Contractor $contractor, Location $location, FlightSimulator $simulator, Deal $deal, Promocode $promocode) {
		$this->contractor = $contractor;
		$this->location = $location;
		$this->simulator = $simulator;
		$this->deal = $deal;
		$this->promocode = $promocode;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = [];
		$recipients[] = $this->deal->email;

		$messageData = [
			'contractor' => $this->contractor ?: new Contractor(),
			'location' => $this->location ?: new Location(),
			'simulator' => $this->simulator ?: new FlightSimulator(),
			'deal' => $this->deal ?: new Deal(),
			'promocode' => $this->promocode ?: new Promocode(),
			'city' => $this->deal->city ?: new City(),
		];

		$subject = env('APP_NAME') . ': промокод на полет';

		Mail::send(['html' => "admin.emails.send_promocode_after_flight"], $messageData, function ($message) use ($subject, $recipients) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
		});
		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - ' . get_class($this) . ': ' . implode(', ', $failures));
			return null;
		}

		$this->promocode->sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->promocode->save();
		
		$task = new Task();
		$task->name = get_class($this);
		$task->email = implode(',', $recipients);
		$task->object_uuid = $this->promocode->uuid;
		$task->save();
	}
}
