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

	public function __construct($name, $phone) {
		$this->name = $name;
		$this->phone = $phone;
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
			'phone' => $this->phone,
		];

		$subject = env('APP_NAME') . ': запрос обратного звонка';

		Mail::send(['html' => "admin.emails.send_callback"], $messageData, function ($message) use ($subject, $recipients) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			/*$message->priority(2);*/
			$message->to($recipients);
		});
	}
}
