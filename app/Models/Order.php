<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string|null $number номер заказа
 * @property int $status_id статус заказа
 * @property int $contractor_id контрагент, совершивший заказ
 * @property string $name имя
 * @property string $phone номер телефона
 * @property string $email e-mail
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property int $product_id продукт
 * @property int $amount стоимость
 * @property int $duration продолжительность полета
 * @property int $promocode_id промокод
 * @property int $certificate_id сертификат
 * @property \datetime|null $flight_at дата и время полета
 * @property bool $is_certificate_order заказ сертификата
 * @property bool $is_unified сертификат действует во всех городах
 * @property string|null $source источник
 * @property array|null $data_json дополнительная информация
 * @property int $user_id пользователь
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\DealPosition $dealPosition
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Promocode|null $promocode
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsCertificateOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsUnified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromocodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
class Order extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'status_id' => 'Статус',
		'contractor_id' => 'Контрагент',
		'name' => 'Имя',
		'phone' => 'Телефон',
		'email' => 'E-mail',
		'city_id' => 'Город',
		'location_id' => 'Локация',
		'product_id' => 'Продукт',
		'amount' => 'Стоимость',
		'duration' => 'Длительность',
		'promocode_id' => 'Промокод',
		'certificate_id' => 'Сертификат',
		'flight_at' => 'Дата полета',
		'is_certificate_order' => 'Заказ сертификата',
		'is_unified' => 'Единый сертификат',
		'source' => 'Источник',
		'data_json' => 'Дополнительная информация',
		'user_id' => 'Пользователь',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
		'certificate_whom' => 'Для кого сертифкат',
		'comment' => 'Комментарий',
	];
	
	const RECEIVED_STATUS = 'received';
	const ON_PHONE_STATUS = 'on_phone';
	const PROCESSED_STATUS = 'processed';
	const CANCELED_STATUS = 'canceled';
	const STATUSES = [
		self::RECEIVED_STATUS,
		self::ON_PHONE_STATUS,
		self::PROCESSED_STATUS,
		self::CANCELED_STATUS,
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'status_id',
		'contractor_id',
		'name',
		'phone',
		'email',
		'city_id',
		'location_id',
		'product_id',
		'amount',
		'duration',
		'promocode_id',
		'certificate_id',
		'flight_at',
		'is_certificate_order',
		'is_unified',
		'source',
		'data_json',
		'user_id',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'flight_at' => 'datetime:Y-m-d H:i',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
		'is_certificate_order' => 'boolean',
		'is_unified' => 'boolean',
	];
	
	public static function boot() {
		parent::boot();
		
		Order::created(function (Order $order) {
			$order->number = $order->generateNumber();
			$order->save();
		});
		
		Order::deleting(function(Order $order) {
			$order->certificate()->delete();
		});
	}
	
	public function status()
	{
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}

	public function contractor()
	{
		return $this->hasOne('App\Models\Contractor', 'id', 'contractor_id');
	}
	
	public function city()
	{
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function location()
	{
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}
	
	public function product()
	{
		return $this->hasOne('App\Models\Product', 'id', 'product_id');
	}

	public function promocode()
	{
		return $this->hasOne('App\Models\Promocode', 'id', 'promocode_id');
	}
	
	public function certificate()
	{
		return $this->hasOne('App\Models\Certificate', 'id', 'certificate_id');
	}
	
	public function dealPosition()
	{
		return $this->belongsTo('App\Models\DealPosition', 'id', 'order_id');
	}
	
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$locationCount = $this->city ? $this->city->locations->count() : 0;
		$cityAlias = $this->city ? $this->city->alias : '';
		$locationAlias = $this->location ? $this->location->alias : '';
		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';
		$alias = ($locationCount > 1) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);
		
		return 'O' . date('y') . $alias . $productTypeAlias . $productDuration . sprintf('%05d', $this->id);
	}
	
	/**
	 * @return array
	 */
	public function format()
	{
		$data = $this->data_json ?? [];
		
		return [
			'id' => $this->id,
			'number' => $this->number,
			'status' => $this->status ? $this->status->name : null,
		];
	}
}
