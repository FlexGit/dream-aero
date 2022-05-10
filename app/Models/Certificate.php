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
		$alias = !$this->city_id ? 'uni' : ($this->city ? mb_strtolower($this->city->alias) : '');
		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';
		
		return 'C' . date('y') . $alias . $productTypeAlias . $productDuration  . sprintf('%05d', $this->id);
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
		$product = $this->product;
		if (!$product) return null;
		
		$productType = $product->productType;
		if (!$productType) return null;
		
		$city = $this->city;
		//if (!$city) return null;

		// если это Единый сертификат
		if (!$city && in_array($productType->alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS])) {
			$certificateTemplateFilePath = 'certificate/template/' . mb_strtoupper($productType->alias) . '_UNI.jpg';
		} else {
			/*$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
			if (!$cityProduct || !$cityProduct->pivot) {
				return null;
			}
			
			$data = json_decode($cityProduct->pivot->data_json, true);
			$certificateTemplateFilePath = $data['certificate_template_file_path'] ?? '';
			if (!isset($certificateTemplateFilePath)) {
				return null;
			}*/
			$certificateTemplateFilePath = 'certificate/template/' . preg_replace("/[^A-Z]/", '', mb_strtoupper($product->alias)) . '_' . mb_strtoupper($city->alias) . '.jpg';
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
					$certificateFile->text($this->number, 910, 485, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(40);
						$font->color('#333333');
					});
					$certificateFile->text($this->created_at->format('d.m.Y'), 1735, 485, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(40);
						$font->color('#333333');
					});
					$certificateFile->text($this->product->duration ?? '-', 1340, 2590, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(56);
						$font->color('#333333');
					});
				} else {
					$certificateFile->text($this->number, 833, 121, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(22);
						$font->color('#333333');
					});
					$certificateFile->text($this->created_at->format('d.m.Y'), 1300, 121, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(22);
						$font->color('#333333');
					});
					$certificateFile->text($this->product->duration ?? '-', 355, 1225, function ($font) use ($fontPath) {
						$font->file($fontPath);
						$font->size(46);
						$font->color('#ffffff');
					});
				}
			break;
			case ProductType::COURSES_ALIAS:
			case ProductType::PLATINUM_ALIAS:
				$certificateFile->text($this->number, 4700, 3022, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(70);
					$font->color('#333333');
				});
				$certificateFile->text($this->created_at->format('d.m.Y'), 6100, 3022, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(70);
					$font->color('#333333');
				});
			break;
			case ProductType::VIP_ALIAS:
				$certificateFile->text($this->number, 1880, 430, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(36);
					$font->color('#333333');
				});
				$certificateFile->text($this->created_at->format('d.m.Y'), 1965, 505, function($font) use ($fontPath) {
					$font->file($fontPath);
					$font->size(36);
					$font->color('#333333');
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
		$positionCount = DealPosition::where('is_certificate_purchase', false)
			->where('certificate_id', $this->id)
			->count();
		
		return (bool)$positionCount;
	}
}
