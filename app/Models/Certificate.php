<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Certificate
 *
 * @property int $id
 * @property string|null $number номер
 * @property int $status_id статус
 * @property \datetime|null $expire_at срок окончания действия сертификата
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property string|null $uuid
 * @property-read \App\Models\Deal $deal
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Query\Builder|Certificate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Certificate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Certificate withoutTrashed()
 * @mixin \Eloquent
 * @property int $city_id
 * @property int $product_id
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\DealPosition|null $dealPosition
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereProductId($value)
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
		'uuid',
		'expire_at',
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

	public function dealPosition()
	{
		return $this->belongsTo(DealPosition::class, 'certificate_id', 'id');
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
}
