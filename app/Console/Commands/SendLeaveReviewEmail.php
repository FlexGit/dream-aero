<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
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
		//\DB::connection()->enableQueryLog();
    	$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->whereNull('leave_review_sent_at')
			->where('stop_at', '<', Carbon::now()->subDay()->format('Y-m-d H:i:s'))
			->where('stop_at', '>', Carbon::now()->subDays(2)->format('Y-m-d H:i:s'))
			->where('stop_at', '>', '2022-09-12 00:00:00')
			->whereHas('contractor', function ($query) {
				return $query->where('is_subscribed', true);
			})
			/*->whereIn('contractor_id', [1])*/
			->oldest()
			->limit(10)
			->get();
		//\Log::debug(\DB::getQueryLog());
    	/** @var Event[] $events */
		foreach ($events as $event) {
			if (!$event->contractor_id) continue;
			
			$contractor = $event->contractor;
			if (!$contractor->is_subscribed) continue;
			
			$deal = $event->deal;
			
			$email = $deal ? $deal->email : ($contractor ? $contractor->email : '');
			if (!$email) continue;
			
			try {
				$recipients = $bcc = [];
				$recipients[] = $email;
				$bcc[] = env('DEV_EMAIL');
				
				$messageData = [
					'event' => $event,
				];
				
				$subject = env('APP_NAME') . ': оставьте отзыв';
				
				Mail::send(['html' => "admin.emails.send_leave_review"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
					/** @var \Illuminate\Mail\Message $message */
					$message->subject($subject);
					$message->to($recipients);
					$message->bcc($bcc);
				});
				$failures = Mail::failures();
				if (!$failures) {
					$event->leave_review_sent_at = Carbon::now()->format('Y-m-d H:i:s');
					$event->save();
				}
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
			
				return 0;
			}
		}
			
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - leave_review_email:send - OK');
    	
        return 0;
    }
}
