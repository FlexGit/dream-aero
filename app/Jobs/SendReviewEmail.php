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
		$recipients = /*$bcc = */[];
		$recipients[] = env('ADMIN_EMAIL');
		//$bcc[] = env('DEV_EMAIL');
		
		$messageData = [
			'name' => $this->name,
			'body' => $this->body,
		];

		$subject = env('APP_NAME') . ': новый отзыв';

		Mail::send(['html' => "admin.emails.send_review"], $messageData, function ($message) use ($subject, $recipients/*, $bcc*/) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
			//$message->bcc($bcc);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - review_email:send - ' . implode(', ', $failures));
			return null;
		}
	}
}
