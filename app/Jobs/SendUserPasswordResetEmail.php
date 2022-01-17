<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\User;
use App\Services\Locker;
use App\Services\Semaphore;
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
		$semaphore = Semaphore::getSemaphoreOrNull('SendUserPasswordResetEmail', 1, 0, 50); // 1 слот для одновременной обработки, 0 секунд ждём свою очередь, 50 секунд даём на обработку
		if (!$semaphore) {
			// пишем в лог проблему на случай, если попыток было слишком много и всё равно требуется начинать job с нуля
			if ($this->attempts() >= 5) {
				\Log::error("Can't execute SendUserPasswordResetEmail for {$this->userId} -- no semaphore slots left at least 5 times in a row, RELEASED ONCE MORE as a clean copy");
				return $this->releaseCleanAttempt(5);
			} else {
				return $this->releaseAgain($this->attempts() * 3);
			}
		}
		
		$user = User::find($this->userId);
		if (!$user) return;
		
		$lock = Locker::getLockOrNull("SendUserPasswordResetEmail_{$user->id}", 15, 100); // 15 секунд ждём лок, 100 секунд даём на обработку
		if (!$lock) {
			if ($this->attempts() <= 4) {
				$this->releaseAgain(10);
			} else {
				$this->releaseCleanAttempt(10);
			}
			return;
		}
		
		// логируем рассылку
		$log = new \Monolog\Logger('user_send_password_reset_notifier');
		try {
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path('logs/user_send_password_reset_notifier.log'), \Monolog\Logger::INFO));
		} catch (\Exception $e) {
			\Log::critical('Cannot create log file for user_send_password_reset_notifier', [$e->getMessage()]);
			$log = \Log::getMonolog();
		}
		
		$messageData = [
			'name' => $user->name,
		];
		
		try {
			$subject = env('APP_NAME') . ': восстановление пароля';
			Mail::send(['html' => "admin.emails.send_password_reset"], $messageData, function ($message) use ($subject, $user) {
				/** @var \Illuminate\Mail\Message $message */
				$message->subject($subject);
				$message->priority(2);
				$message->to($user->email);
			});
			if (in_array($user->email, Mail::failures())) {
				throw new \Exception("Email $user->email in a failed list");
			}
		} catch (\Exception $e) {
			$log->error('ERROR on User password reset send ', ['id' => $user->id, 'email' => $user->email, 'msg' => $e->getMessage()]);
		}
	}
}
