<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\DealPosition
 *
 * @property int $id
 * @property string $number номер позиции сделки
 * @property int $deal_id сделка
 * @property int $status_id статус
 * позиции сделки
 * @property int $order_id заказ
 * @property int $product_id продукт
 * @property int $certificate_id сертификат
 * @property int $duration продолжительность полета
 * @property int $amount стоимость
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property \datetime|null $flight_at дата и время полета
 * @property \datetime|null $invite_sent_at последняя дата отправки приглашения на e-mail
 * @property \datetime|null $certificate_sent_at последняя дата отправки сертификата на e-mail
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Deal|null $deal
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition newQuery()
 * @method static \Illuminate\Database\Query\Builder|DealPosition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereInviteSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DealPosition withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DealPosition withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
 */
class DealPosition extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Нмер',
		'deal_id' => 'Сделка',
		'status_id' => 'Статус',
		'order_id' => 'Заявка',
		'product_id' => 'Продукт',
		'certificate_id' => 'Сертификат',
		'duration' => 'Длительность',
		'amount' => 'Стоимость',
		'city_id' => 'Город',
		'location_id' => 'Локация',
		'flight_at' => 'Дата полета',
		'invite_sent_at' => 'Дата отправки приглашения',
		'certificate_sent_at' => 'Дата отправки сертификата',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
		'certificate_whom' => 'Для кого сертифкат',
		'comment' => 'Комментарий',
	];
	
	const CREATED_STATUS = 'created';
	const PAY_WAITING_STATUS = 'pay_waiting';
	const CALENDAR_STATUS = 'calendar';
	const PAUSED_STATUS = 'paused';
	const CANCELED_STATUS = 'canceled';
	const STATUSES = [
		self::CREATED_STATUS,
		self::PAY_WAITING_STATUS,
		self::CALENDAR_STATUS,
		self::PAUSED_STATUS,
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
		'deal_id',
		'status_id',
		'order_id',
		'product_id',
		'certificate_id',
		'duration',
		'amount',
		'city_id',
		'location_id',
		'flight_at',
		'invite_sent_at',
		'certificate_sent_at',
		'data_json',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'flight_at' => 'datetime:Y-m-d H:i',
		'invite_sent_at' => 'datetime:Y-m-d H:i',
		'certificate_sent_at' => 'datetime:Y-m-d H:i',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();
		
		DealPosition::created(function (DealPosition $dealPosition) {
			$dealPosition->number = $dealPosition->generateNumber();
			$dealPosition->save();
		});
		
		DealPosition::deleting(function(DealPosition $dealPosition) {
			$dealPosition->certificate()->delete();
		});
	}
	
	public function deal()
	{
		return $this->belongsTo('App\Models\Deal', 'deal_id', 'id');
	}

	public function product()
	{
		return $this->hasOne('App\Models\Product', 'id', 'product_id');
	}
	
	public function certificate()
	{
		return $this->hasOne('App\Models\Certificate', 'id', 'certificate_id');
	}

	public function city()
	{
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function location()
	{
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}
	
	public function status()
	{
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}
	
	public function order()
	{
		return $this->hasOne('App\Models\Order', 'id', 'order_id');
	}
	
	public function bills()
	{
		return $this->hasMany('App\Models\Bill', 'deal_position_id', 'id');
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
		$alias = ($locationCount > 1 && $this->location) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);
		
		return 'D' . date('y') . $alias . $productTypeAlias . $productDuration . sprintf('%05d', $this->id);
	}
}
