<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendPersonalFeedbackEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $name;
	protected $parentName;
	protected $age;
	protected $phone;
	protected $email;
	protected $messageText;
	protected $cityName;

	public function __construct($name, $parentName, $age, $phone, $email, $cityName) {
		$this->name = $name;
		$this->parentName = $parentName;
		$this->age = $age;
		$this->phone = $phone;
		$this->email = $email;
		$this->cityName = $cityName;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$recipients = [];
		$recipients[] = env('DEV_EMAIL');

		$messageData = [
			'name' => $this->name ?? '',
			'parentName' => $this->parentName ?? '',
			'age' => $this->age ?? '',
			'phone' => $this->phone ?? '',
			'email' => $this->email ?? '',
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
