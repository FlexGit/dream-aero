<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Certificate
 *
 * @property int $id
 * @property string|null $number номер
 * @property int $status_id статус
 * @property int $city_id город
 * @property int $product_id продукт
 * @property string|null $uuid uuid
 * @property \datetime|null $expire_at срок окончания действия сертификата
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\DealPosition|null $position
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Query\Builder|Certificate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Certificate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Certificate withoutTrashed()
 * @mixin \Eloquent
 * @property \datetime|null $sent_at
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereSentAt($value)
 */
class Certificate extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'status_id' => 'Статус',
		'city_id' => 'Город',
		'product_id' => 'Продукт',
		'uuid' => 'Uuid',
		'expire_at' => 'Срок окончания действия',
		'sent_at' => 'Дата отправки сертификата',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	const CREATED_STATUS = 'certificate_created';
	const REGISTERED_STATUS = 'certificate_registered';
	const RETURNED_STATUS = 'certificate_returned';
	const CANCELED_STATUS = 'certificate_canceled';
	const STATUSES = [
		self::CREATED_STATUS,
		self::REGISTERED_STATUS,
		self::RETURNED_STATUS,
		self::CANCELED_STATUS,
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	protected $dontKeepRevisionOf = ['uuid', 'data_json'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'status_id',
		'city_id',
		'product_id',
		'expire_at',
		'sent_at',
		'uuid',
		'data_json',
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'expire_at' => 'datetime:Y-m-d H:i:s',
		'sent_at' => 'datetime:Y-m-d H:i',
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();
		
		Certificate::created(function (Certificate $certificate) {
			$certificate->number = $certificate->generateNumber();
			$certificate->uuid = $certificate->generateUuid();
			$certificate->save();
		});
	}
	
	public function status()
	{
		return $this->hasOne(Status::class, 'id', 'status_id');
	}

	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}

	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function position()
	{
		return $this->belongsTo(DealPosition::class, 'id', 'certificate_id');
	}
	
	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$cityAlias = !$this->city_id ? 'uni' : ($this->city ? mb_strtolower($this->city->alias) : '');
		$productAlias = ($this->product) ? mb_strtoupper(substr($this->product->alias, 0, 1)) : '';
		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';
		
		return 'C' . date('y') . $cityAlias . (!in_array($this->product->productType->alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]) ? $productTypeAlias : '') . $productAlias  . (in_array($this->product->productType->alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]) ? $productDuration : '') . sprintf('%05d', $this->id);
	}
	
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function generateUuid()
	{
		return (string)\Webpatser\Uuid\Uuid::generate();
	}
	
	/**
	 * @return Certificate|null
	 */
	public function generateFile()
	{
		$position = $this->position;
		if (!$position) return null;
		
		$deal = $position->deal;
		if (!$deal) return null;
		
		if ($deal->balance() < 0) {
			$bill = $position->bill;
			if (!$bill) return null;
			if (!$bill->payed_at) return null;
			
			$billStatus = $bill->status;
			if (!$billStatus) return null;
			if ($billStatus->alias != Bill::PAYED_STATUS) return null;
		}
		
		$product = $this->product;
		if (!$product) return null;
		
		$productType = $product->productType;
		if (!$productType) return null;
		
		$city = $this->city;
		
		if (!$city && in_array($productType->alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS])) {
			// единый Сертификат
			$certificateTemplateFilePath = 'certificate/template/' . mb_strtoupper($productType->alias) . '_UNI.jpg';
		} elseif ($productType->alias == ProductType::VIP_ALIAS) {
			// vip
			$certificateTemplateFilePath = 'certificate/template/VIP_' . mb_strtoupper($product->alias) . '.jpg';
		} else {
			$certificateTemplateFilePath = 'certificate/template/' . preg_replace("/[^A-Z]/", '', mb_strtoupper($product->alias)) . '_' . ($city ? mb_strtoupper($city->alias) : 'UNKNOWN') . '.jpg';
		}
		
		if (!Storage::disk('private')->exists($certificateTemplateFilePath)) {
			return null;
		}
		
		$certificateTemplateFilePath = Storage::disk('private')->path($certificateTemplateFilePath);
		
		$certificateFile = Image::make($certificateTemplateFilePath)->encode('jpg');
		$fontPath = public_path('assets/fonts/GothamProRegular/GothamProRegular.ttf');
		
		switch ($productType->alias) {
			case ProductType::REGULAR_ALIAS:
			case ProductType::ULTIMATE_ALIAS:
				// Единый сертификат
				if (!$city) {
					$certificateFile->text($this->number, 935, 485, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(40);
						$font->color('#000000');
					});
					$certificateFile->text($bill->payed_at->format('d.m.Y'), 1735, 485, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(40);
						$font->color('#000000');
					});
					$certificateFile->text($product->duration ?? '-', 1340, 2590, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(56);
						$font->color('#000000');
					});
				} else {
					switch ($city->alias) {
						case City::MSK_ALIAS:
							$certificateFile->text($this->number, 835, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($bill->payed_at->format('d.m.Y'), 1300, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($product->duration ?? '-', 355, 1210, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(46);
								$font->color('#000000');
							});
						break;
						case City::SPB_ALIAS:
							$certificateFile->text($this->number, 840, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($bill->payed_at->format('d.m.Y'), 1300, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($product->duration ?? '-', 355, 1225, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(46);
								$font->color('#000000');
							});
						break;
						case City::VRN_ALIAS:
						case City::NNV_ALIAS:
						case City::SAM_ALIAS:
							$certificateFile->text($this->number, 855, 155, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($bill->payed_at->format('d.m.Y'), 1300, 155, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($product->duration ?? '-', 370, 1370, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(46);
								$font->color('#000000');
							});
						break;
						case City::EKB_ALIAS:
							$certificateFile->text($this->number, 850, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($bill->payed_at->format('d.m.Y'), 1300, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($product->duration ?? '-', 355, 1225, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(46);
								$font->color('#000000');
							});
						break;
						case City::KZN_ALIAS:
						case City::KRD_ALIAS:
						case City::NSK_ALIAS:
							$certificateFile->text($this->number, 850, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($bill->payed_at->format('d.m.Y'), 1300, 121, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($product->duration ?? '-', 355, 1370, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(46);
								$font->color('#000000');
							});
						break;
						case City::KHV_ALIAS:
							$certificateFile->text($this->number, 840, 127, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($bill->payed_at->format('d.m.Y'), 1300, 127, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(22);
								$font->color('#000000');
							});
							$certificateFile->text($product->duration ?? '-', 360, 1370, function ($font) use ($fontPath) {
								$font->file($fontPath);
								$font->size(46);
								$font->color('#000000');
							});
						break;
					}
				}
			break;
			case ProductType::COURSES_ALIAS:
				$certificateFile->text($this->number, 1560, 1005, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(24);
					$font->color('#000000');
				});
				$certificateFile->text($bill->payed_at->format('d.m.Y'), 2030, 1005, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(24);
					$font->color('#000000');
				});
			break;
			case ProductType::VIP_ALIAS:
				$certificateFile->text($this->number, 1880, 430, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(36);
					$font->color('#000000');
				});
				$certificateFile->text($bill->payed_at->format('d.m.Y'), 1965, 505, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(36);
					$font->color('#000000');
				});
			break;
		}
		
		$certificateFileName = $this->uuid . '.jpg';
		if (!$certificateFile->save(storage_path('app/private/certificate/' . $certificateFileName))) {
			return null;
		}
		
		$this->data_json = [
			'certificate_file_path' => 'certificate/' . $certificateFileName,
		];
		if (!$this->save()) {
			return null;
		}
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function wasUsed()
	{
		/*$positionCount = DealPosition::where('is_certificate_purchase', false)
			->where('certificate_id', $this->id)
			->count();
		
		return (bool)$positionCount;*/
		return false;
	}
}
