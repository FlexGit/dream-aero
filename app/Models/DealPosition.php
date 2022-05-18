<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\DealPosition
 *
 * @property int $id
 * @property string|null $number номер
 * @property int $deal_id сделка
 * @property int $product_id продукт
 * @property int $certificate_id сертификат
 * @property int $duration продолжительность полета
 * @property int $amount стоимость
 * @property int $currency_id валюта
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property int $flight_simulator_id авиатренажер
 * @property int $promo_id акция
 * @property int $promocode_id промокод
 * @property bool $is_certificate_purchase покупка сертификата
 * @property \datetime|null $flight_at дата и время полета
 * @property string|null $source источник
 * @property string|null $aeroflot_transaction_type
 * @property string|null $aeroflot_transaction_order_id
 * @property string|null $aeroflot_card_number
 * @property int $aeroflot_bonus_amount
 * @property string|null $uuid
 * @property string|null $aeroflot_status
 * @property string|null $aeroflot_state
 * @property int $user_id пользователь
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AeroflotBonusLog[] $aeroflotBonusLog
 * @property-read int|null $aeroflot_bonus_log_count
 * @property-read \App\Models\Bill|null $bill
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Currency|null $currency
 * @property-read \App\Models\Deal|null $deal
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Promo|null $promo
 * @property-read \App\Models\Promocode|null $promocode
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Score|null $score
 * @property-read \App\Models\FlightSimulator|null $simulator
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition newQuery()
 * @method static \Illuminate\Database\Query\Builder|DealPosition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotBonusAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotTransactionOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereIsCertificatePurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition wherePromoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition wherePromocodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|DealPosition withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DealPosition withoutTrashed()
 * @mixin \Eloquent
 */
class DealPosition extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'deal_id' => 'Сделка',
		'product_id' => 'Продукт',
		'certificate_id' => 'Сертификат',
		'duration' => 'Длительность',
		'amount' => 'Стоимость',
		'currency_id' => 'Валюта',
		'city_id' => 'Город',
		'location_id' => 'Локация',
		'flight_simulator_id' => 'Авиатренажер',
		'promo_id' => 'Акция',
		'promocode_id' => 'Промокод',
		'is_certificate_purchase' => 'Покупка сертификата',
		'flight_at' => 'Дата полета',
		'source' => 'Источник',
		'aeroflot_transaction_type' => 'Тип транзакции',
		'aeroflot_transaction_order_id' => 'ID транзакции/заказа',
		'aeroflot_card_number' => 'Номер карты',
		'aeroflot_bonus_amount' => 'Сумма бонуса',
		'aeroflot_status' => 'Статус операции',
		'aeroflot_state' => 'Состояние заказа',
		'uuid' => 'Uuid',
		'user_id' => 'Пользователь',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
		'certificate_whom' => 'Для кого сертифкат',
		'comment' => 'Комментарий',
	];
	
	const ADMIN_SOURCE = 'admin';
	const WEB_SOURCE = 'web';
	const MOB_SOURCE = 'api';
	const SOURCES = [
		self::ADMIN_SOURCE => 'Админка',
		self::WEB_SOURCE => 'Web',
		self::MOB_SOURCE => 'Mob',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	protected $dontKeepRevisionOf = ['source'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'deal_id',
		'product_id',
		'certificate_id',
		'duration',
		'amount',
		'currency_id',
		'city_id',
		'location_id',
		'flight_simulator_id',
		'promo_id',
		'promocode_id',
		'is_certificate_purchase',
		'flight_at',
		'user_id',
		'source',
		'uuid',
		'aeroflot_transaction_type',
		'aeroflot_transaction_order_id',
		'aeroflot_card_number',
		'aeroflot_bonus_amount',
		'aeroflot_status',
		'aeroflot_state',
		'data_json',
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
		'is_certificate_purchase' => 'boolean',
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();

		DealPosition::created(function (DealPosition $dealPosition) {
			$dealPosition->number = $dealPosition->generateNumber();
			$dealPosition->uuid = (string)\Webpatser\Uuid\Uuid::generate();
			$dealPosition->save();
		});

		DealPosition::saved(function (DealPosition $dealPosition) {
			if (!$dealPosition->user_id && $dealPosition->source == Deal::ADMIN_SOURCE) {
				$dealPosition->user_id = \Auth::user()->id;
				$dealPosition->save();
			}
		});

		DealPosition::deleting(function(DealPosition $position) {
			//$position->certificate()->delete();
			/*$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			
			$certificate = $position->certificate;
			$certificate->status_id = $certificateStatus->id;
			$certificate->save();*/
		});
	}
	
	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function currency()
	{
		return $this->hasOne(Currency::class, 'id', 'currency_id');
	}

	public function certificate()
	{
		return $this->hasOne(Certificate::class, 'id', 'certificate_id');
	}

	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}

	public function location()
	{
		return $this->hasOne(Location::class, 'id', 'location_id');
	}

	public function simulator()
	{
		return $this->hasOne(FlightSimulator::class, 'id', 'flight_simulator_id');
	}

	public function deal()
	{
		return $this->belongsTo(Deal::class);
	}
	
	public function event()
	{
		return $this->hasOne(Event::class, 'deal_position_id', 'id');
	}
	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
	
	public function promocode()
	{
		return $this->hasOne(Promocode::class, 'id', 'promocode_id');
	}
	
	public function promo()
	{
		return $this->hasOne(Promo::class, 'id', 'promo_id');
	}

	public function score()
	{
		return $this->belongsTo(Score::class);
	}
	
	public function bill()
	{
		return $this->belongsTo(Bill::class, 'id', 'deal_position_id');
	}
	
	public function bills()
	{
		return $this->hasMany(Bill::class, 'deal_position_id', 'id');
	}
	
	public function aeroflotBonusLog()
	{
		return $this->hasMany(AeroflotBonusLog::class, 'deal_position_id', 'id');
	}

	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$locationCount = ($this->city && $this->city->locations) ? $this->city->locations->count() : 1;

		$cityAlias = $this->city ? mb_strtolower($this->city->alias) : '';
		$locationAlias = $this->location ? $this->location->alias : '';

		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';

		$alias = ($locationCount > 1 && $locationAlias) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);

		return 'P' . date('y') . $alias . $productTypeAlias . $productDuration . sprintf('%05d', $this->id);
	}
}
