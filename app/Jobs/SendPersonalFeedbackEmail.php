<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Contractor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendPersonalFeedbackEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $name;
	protected $phone;
	protected $email;
	protected $messageText;
	protected $cityName;

	public function __construct($name, $phone, $email, $messageText, $cityName) {
		$this->name = $name;
		$this->phone = $phone;
		$this->email = $email;
		$this->messageText = $messageText;
		$this->cityName = $cityName;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = [];
		$recipients[] = env('ADMIN_EMAIL');

		$messageData = [
			'name' => $this->name ?? '',
			'phone' => $this->phone ?? '',
			'email' => $this->email ?? '',
			'messageText' => $this->messageText ?? '',
			'cityName' => $this->cityName ?? '',
		];

		$subject = env('APP_NAME') . ': сообщение обратной связи';

		Mail::send(['html' => "admin.emails.send_personal_feedback"], $messageData, function ($message) use ($subject, $recipients) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
		});
	}
}
