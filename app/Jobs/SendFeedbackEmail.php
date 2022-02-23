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
	protected $message;

	public function __construct(Contractor $contractor, $message) {
		$this->contractorId = $contractor->id;
		$this->fio = $contractor->fio();
		$this->email = $contractor->email;
		$this->phone = $contractor->phone;
		$this->city = $contractor->city ? $contractor->city->name : '';
		$this->message = $message;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$contractor = Contractor::find($this->contractorId);
		if (!$contractor) return;
		
		$recipients = [];
		$recipients[] = 'anton.s@dream-aero.com';
		$recipients[] = 'webmanage@inbox.ru';

		$messageData = [
			'fio' => $this->fio,
			'email' => $this->email ?? '',
			'phone' => $this->phone ?? '',
			'city' => $this->city ?? '',
			'message' => $this->message ?? '',
			'source' => 'mob',
		];

		try {
			$subject = env('APP_NAME') . ': сообщение обратной связи';

			Mail::send(['html' => "admin.emails.send_feedback"], $messageData, function ($message) use ($subject, $recipients) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				/*$message->priority(2);*/
				$message->to($recipients);
			});

			foreach ($recipients as $recipient) {
				if (in_array($recipient, Mail::failures())) {
					throw new \Exception("Email $recipient in a failed list");
				}
			}
		} catch (\Exception $e) {
			/*$log->error('ERROR on Deal send ', ['number' => $deal->number, 'email' => $deal->email, 'msg' => $e->getMessage()]);*/
		}
	}
}
