<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendReviewEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $name;
	protected $body;

	public function __construct($name, $body) {
		$this->name = $name;
		$this->body = $body;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = [];
		$recipients[] = env('ADMIN_EMAIL');
		
		$messageData = [
			'name' => $this->name,
			'body' => $this->body,
		];

		$subject = env('APP_NAME') . ': новый отзыв';

		Mail::send(['html' => "admin.emails.send_review"], $messageData, function ($message) use ($subject, $recipients) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - ' . get_class($this) . ': ' . implode(', ', $failures));
			return null;
		}
	}
}
