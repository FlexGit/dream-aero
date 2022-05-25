<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendCallbackEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $name;
	protected $phone;
	protected $city;

	public function __construct($name, $phone, $city) {
		$this->name = $name;
		$this->phone = $phone;
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
			'phone' => $this->phone,
		];

		$subject = env('APP_NAME') . ': запрос обратного звонка';

		Mail::send(['html' => "admin.emails.send_callback"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->to($recipients);
			$message->bcc($bcc);
		});
	}
}
