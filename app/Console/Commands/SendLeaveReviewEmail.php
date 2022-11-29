<?php

namespace App\Console\Commands;

use App\Models\Contractor;
use App\Models\Event;
use App\Models\Task;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Mail;
use Throwable;

class SendLeaveReviewEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave_review_email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor with invitation to leave review';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->whereNull('leave_review_sent_at')
			->where('stop_at', '<', Carbon::now()->subDay()->format('Y-m-d H:i:s'))
			->where('stop_at', '>', Carbon::now()->subDays(2)->format('Y-m-d H:i:s'))
			->where('stop_at', '>', '2022-11-01 00:00:00')
			->whereHas('contractor', function ($query) {
				return $query->where('is_subscribed', true)
					->where('email', '!=', Contractor::ANONYM_EMAIL);
			})
			->whereHas('deal', function ($query) {
				return $query->where('email', '!=', Contractor::ANONYM_EMAIL);
			})
			->oldest()
			->limit(10)
			->get();
    	/** @var Event[] $events */
		foreach ($events as $event) {
			$deal = $event->deal;
			$email = $deal->email;
			if (!$email) continue;
			
			try {
				DB::beginTransaction();
				
				$recipients = [];
				$recipients[] = $email;
				
				$messageData = [
					'event' => $event,
				];
				
				$subject = env('APP_NAME') . ': оставьте отзыв';
				
				Mail::send(['html' => "admin.emails.send_leave_review"], $messageData, function ($message) use ($subject, $recipients) {
					/** @var \Illuminate\Mail\Message $message */
					$message->subject($subject);
					$message->to($recipients);
				});
				$failures = Mail::failures();
				if ($failures) {
					DB::rollback();
					
					return 0;
				}
				
				$event->leave_review_sent_at = Carbon::now()->format('Y-m-d H:i:s');
				$event->save();
				
				$task = new Task();
				$task->name = get_class($this);
				$task->email = $email;
				$task->object_uuid = $event->uuid;
				$task->save();
				
				DB::commit();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . get_class($this) . ': ' . $e->getMessage());
			
				return 0;
			}
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - ' . get_class($this) . ': OK');
    	
        return 0;
    }
}
