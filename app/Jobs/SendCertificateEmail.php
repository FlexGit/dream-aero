<?php

namespace App\Jobs;

use App\Jobs\QueueExtension\ReleaseHelperTrait;
use App\Models\Bill;
use App\Models\Certificate;
use App\Models\City;
use App\Models\Contractor;
use App\Models\PaymentMethod;
use App\Models\ProductType;
use App\Models\Task;
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
		\Log::debug('send certificate: label1');
		if ($this->certificate->sent_at) {
			return null;
		}
		\Log::debug('send certificate: label2');
		
		$certificateFilePath = isset($this->certificate->data_json['certificate_file_path']) ? $this->certificate->data_json['certificate_file_path'] : '';
		$certificateFileExists = Storage::disk('private')->exists($certificateFilePath);

		// если файла сертификата по какой-то причине не оказалось, генерим его
		if (!$certificateFilePath || !$certificateFileExists) {
			$this->certificate = $this->certificate->generateFile();
			if (!$this->certificate) {
				return null;
			}
		}
		\Log::debug('send certificate: label3');
		$position = $this->certificate->position()
			->where('is_certificate_purchase', true)
			->first();
		if (!$position) return null;
		\Log::debug('send certificate: label4');
		$deal = $position->deal;
		if (!$deal) return null;
		\Log::debug('send certificate: label5');
		$balance = $position->balance();
		if ($balance < 0) return null;
		\Log::debug('send certificate: label6');
		$isOnlinePaid = false;
		foreach ($position->bills as $bill) {
			if ($bill->paymentMethod && $bill->paymentMethod->alias == PaymentMethod::ONLINE_ALIAS) {
				$isOnlinePaid = true;
			}
		}
		if (!$isOnlinePaid) return null;
		\Log::debug('send certificate: label7');
		$product = $position->product;
		if (!$product) return null;
		\Log::debug('send certificate: label8');
		$productType = $product->productType;
		if (!$productType) return null;
		\Log::debug('send certificate: label9');
		$contractor = $deal->contractor;
		if (!$contractor) return null;
		\Log::debug('send certificate: label10');
		if (!$deal->name || !$deal->email || $deal->name == Contractor::ANONYM_EMAIL) {
			return null;
		}
		\Log::debug('send certificate: label11');
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
		$x = (mb_strlen($period) == 1) ? 341 : 339;
		$certificateRulesFile->text($period, $x, 250, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(17);
			$font->color('#000000');
		});
		$certificateRulesFile->text($peopleCount, 784, 312, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(17);
			$font->color('#000000');
		});
		
		$fontPath = public_path('assets/fonts/Montserrat/Montserrat-ExtraBold.ttf');
		$certificateRulesFile->text($cityPhone ?? '', 660, 406, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(17);
			$font->color('#000000');
		});
		
		if (!$certificateRulesFile->save(storage_path('app/private/rule/' . $certificateRulesFileName))) {
			return null;
		}
		\Log::debug('send certificate: label12');
		$messageData = [
			'certificate' => $this->certificate,
			'name' => $deal->name,
			'city' => $deal->city,
		];
		
		$recipients = $bcc = [];
		$recipients[] = $deal->email;
		if ($deal->city && $deal->city->email) {
			$bcc[] = $deal->city->email;
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
		\Log::debug('send certificate: label13');
		$failures = Mail::failures();
		if ($failures) {
			\Log::debug('500 - ' . get_class($this) . ': ' . implode(', ', $failures));
			return null;
		}
		\Log::debug('send certificate: label14');
		$this->certificate->sent_at = Carbon::now()->format('Y-m-d H:i:s');
		$this->certificate->save();
		
		$task = new Task();
		$task->name = get_class($this);
		$task->email = $deal->email;
		$task->object_uuid = $this->certificate->uuid;
		$task->save();
	}
}
