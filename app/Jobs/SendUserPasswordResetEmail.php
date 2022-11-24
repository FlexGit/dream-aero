<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\User;
use App\Models\Deal;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendUserPasswordResetEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;
	
	protected $userId;
	
	public function __construct(Deal $user) {
		$this->userId = $user->id;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$user = User::find($this->userId);
		if (!$user) return;
		
		$messageData = [
			'name' => $user->name ?? '',
		];
		
		$subject = env('APP_NAME') . ': восстановление пароля';

		Mail::send(['html' => "admin.emails.send_password_reset"], $messageData, function ($message) use ($subject, $user) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->priority(2);
			$message->to($user->email);
		});

		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - ' . get_class($this) . ': ' . implode(', ', $failures));
		}
		
		if (in_array($user->email, Mail::failures())) {
			throw new \Exception("Email $user->email in a failed list");
		}
	}
}
