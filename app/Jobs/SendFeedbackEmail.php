<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Contractor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendFeedbackEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $contractorId;
	protected $fio;
	protected $email;
	protected $phone;
	protected $city;
	protected $messageText;

	public function __construct(Contractor $contractor, $messageText) {
		$this->contractorId = $contractor->id;
		$this->fio = $contractor->fio();
		$this->email = $contractor->email;
		$this->phone = $contractor->phone;
		$this->city = $contractor->city ? $contractor->city->name : '';
		$this->messageText = $messageText;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$contractor = Contractor::find($this->contractorId);
		if (!$contractor) return;
		
		$recipients = [];
		$recipients[] = env('ADMIN_EMAIL');

		$messageData = [
			'fio' => $this->fio,
			'email' => $this->email ?? '',
			'phone' => $this->phone ?? '',
			'city' => $this->city ?? '',
			'messageText' => $this->messageText ?? '',
			'source' => 'mob',
		];

		$subject = env('APP_NAME') . ': сообщение обратной связи';

		Mail::send(['html' => "admin.emails.send_feedback"], $messageData, function ($message) use ($subject, $recipients) {
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
