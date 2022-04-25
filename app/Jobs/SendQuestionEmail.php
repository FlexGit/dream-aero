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

	public function __construct($name, $email, $body) {
		$this->name = $name;
		$this->email = $email;
		$this->body = $body;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = [];
		//$recipients[] = 'anton.s@dream-aero.com';
		$recipients[] = 'webmanage@inbox.ru';

		$messageData = [
			'name' => $this->name,
			'email' => $this->email,
			'body' => $this->body,
		];

		$subject = env('APP_NAME') . ': новое сообщение';

		Mail::send(['html' => "admin.emails.send_question"], $messageData, function ($message) use ($subject, $recipients) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			/*$message->priority(2);*/
			$message->to($recipients);
		});
	}
}
