<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Certificate;
use App\Models\City;
use App\Models\ProductType;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
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
		$certificateFileExists = Storage::disk('private')->exists($certificateFilePath);

		// если файла сертификата по какой-то причине не оказалось, генерим его
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
		
		$product = $position->product;
		if (!$product) return null;
		
		$productType = $product->productType;
		if (!$productType) return null;
		
		$contractor = $deal->contractor;
		if (!$contractor) return null;
		
		$dealEmail = $deal->email ?? '';
		$dealName = $deal->name ?? '';
		$dealCity = $deal->city;
		$contractorEmail = $contractor->email ?? '';
		$contractorName = $contractor->name ?? '';
		if (!$dealEmail && !$contractorEmail) {
			return null;
		}
		
		$city = $this->certificate->city;
		if ($city) {
			$cityPhone = $city->phone;
			$certificateRulesFileName = 'RULES_' . mb_strtoupper($city->alias) . '.jpg';
		} else {
			$cityPhone = ' ' . env('UNI_CITY_PHONE');
			$certificateRulesFileName = 'RULES_UNI.jpg';
			$city = HelpFunctions::getEntityByAlias(City::class, City::MSK_ALIAS);
		}

		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		$dataJson = json_decode($cityProduct->pivot->data_json, true);
		$period = (is_array($dataJson) && array_key_exists('certificate_period', $dataJson)) ? $dataJson['certificate_period'] : 6;
		$peopleCount = ($productType->alias == ProductType::VIP_ALIAS) ? 2 : 3;

		$certificateRulesTemplateFilePath = Storage::disk('private')->path('rule/RULES_CERTIFICATE_TEMPLATE.jpg');
		$certificateRulesFile = Image::make($certificateRulesTemplateFilePath)->encode('jpg');

		$fontPath = public_path('assets/fonts/Montserrat/Montserrat-Medium.ttf');
		$certificateRulesFile->text($period, 340, 250, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(18);
			$font->color('#000000');
		});
		$certificateRulesFile->text($peopleCount, 783, 312, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(18);
			$font->color('#000000');
		});
		
		$fontPath = public_path('assets/fonts/Montserrat/Montserrat-ExtraBold.ttf');
		$certificateRulesFile->text($cityPhone ?? '', 638, 407, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(18);
			$font->color('#000000');
		});
		
		if (!$certificateRulesFile->save(storage_path('app/private/rule/' . $certificateRulesFileName))) {
			return null;
		}

		$messageData = [
			'certificate' => $this->certificate,
			'name' => $dealName ?: $contractorName,
			'city' => $dealCity ?? null,
		];
		
		$recipients = $bcc = [];
		$recipients[] = $dealEmail ?: $contractorEmail;
		if ($dealCity && $dealCity->email) {
			$bcc[] = $dealCity->email;
		}

		$subject = env('APP_NAME') . ': сертификат на полет';
		
		Mail::send(['html' => "admin.emails.send_certificate"], $messageData, function ($message) use ($subject, $recipients, $certificateRulesFileName, $bcc) {
			/** @var \Illuminate\Mail\Message $message */
			$message->subject($subject);
			$message->attach(Storage::disk('private')->path($this->certificate->data_json['certificate_file_path']));
			$message->attach(Storage::disk('private')->path('rule/RULES_MAIN.jpg'));
			$message->attach(Storage::disk('private')->path('rule/' . $certificateRulesFileName));
			$message->to($recipients);
			$message->bcc($bcc);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return null;
		}
		
		$this->certificate->sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->certificate->save();
	}
}
