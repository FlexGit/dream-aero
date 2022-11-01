<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendQuestionEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $name;
	protected $email;
	protected $body;
	protected $city;

	public function __construct($name, $email, $body, $city) {
		$this->name = $name;
		$this->email = $email;
		$this->body = $body;
		$this->city = $city;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = $bcc = [];
		$recipients[] = $this->city->email ?: env('ADMIN_EMAIL');
		//$bcc[] = env('DEV_EMAIL');

		$messageData = [
			'name' => $this->name,
			'email' => $this->email,
			'body' => $this->body,
		];

		$subject = env('APP_NAME') . ': новое сообщение';

		Mail::send(['html' => "admin.emails.send_question"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
			$message->bcc($bcc);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - question_email:send - ' . implode(', ', $failures));
			return null;
		}
	}
}
