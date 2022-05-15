<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Mail;

class SendCertificateEmail extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels, ReleaseHelperTrait;

	protected $certificate;

	public function __construct(Certificate $certificate) {
		$this->certificate = $certificate;
	}
	
	/**
	 * @return int|void
	 */
	public function handle() {
		$certificateFilePath = isset($this->certificate->data_json['certificate_file_path']) ? $this->certificate->data_json['certificate_file_path'] : '';
		if ($certificateFilePath) {
			$certificateFileExists = Storage::disk('private')->exists($certificateFilePath);
		}
		if (!$certificateFilePath || !$certificateFileExists) {
			$this->certificate = $this->certificate->generateFile();
			if (!$this->certificate) {
				return null;
			}
		}

		$position = $this->certificate->position;
		if (!$position) return null;
		
		$deal = $position->deal;
		if (!$deal) return null;
		
		$contractor = $deal->contractor;
		if (!$contractor) return null;
		
		$dealEmail = $deal->email ?? '';
		$dealName = $deal->name ?? '';
		$contractorEmail = $contractor->email ?? '';
		$contractorName = $contractor->name ?? '';
		if (!$dealEmail && !$contractorEmail) {
			return null;
		}
		
		$city = $this->certificate->city;
		if ($city) {
			$rulesFileName = 'RULES_' . mb_strtoupper($city->alias);
		} else {
			$rulesFileName = 'RULES_MAIN';
		}
		
		$messageData = [
			'name' => $dealName ?: $contractorName,
			'city' => $position->city ?? null,
		];
		
		$recipients = [];
		$recipients[] = $dealEmail ?: $contractorEmail;

		$subject = env('APP_NAME') . ': сертификат на полет';
		
		Mail::send(['html' => "admin.emails.send_certificate"], $messageData, function ($message) use ($subject, $recipients, $rulesFileName) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->attach(Storage::disk('private')->path($this->certificate->data_json['certificate_file_path']));
			$message->attach(Storage::disk('private')->path('rule/' . $rulesFileName . '.jpg'));
			$message->to($recipients);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return null;
		}
		
		$this->certificate->sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->certificate->save();
	}
}
