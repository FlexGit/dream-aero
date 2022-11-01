<?php

namespace App\Models;

use App\Services\AeroflotBonusService;
use App\Services\HelpFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\DealPosition
 *
 * @property int $id
 * @property string|null $number
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
 * @property \Illuminate\Support\Carbon|null $flight_at дата и время полета
 * @property string|null $source источник
 * @property string|null $uuid
 * @property int $user_id пользователь
 * @property array|null $data_json дополнительная информация
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Bill|null $bill
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
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
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateId($value)
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
 * @property string|null $aeroflot_transaction_type тип транзакции Аэрофлот Бонус
 * @property string|null $aeroflot_transaction_order_id id транзакции/заказа Аэрофлот Бонус
 * @property string|null $aeroflot_card_number номер карты Аэрофлот Бонус
 * @property int $aeroflot_bonus_amount сумма транзакции Аэрофлот Бонус
 * @property string|null $aeroflot_status статус транзакции Аэрофлот Бонус
 * @property string|null $aeroflot_state состояние транзакции списания милей Аэрофлот Бонус
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotBonusAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotTransactionOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAeroflotTransactionType($value)
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
			if ($dealPosition->user && $dealPosition->created_at != $dealPosition->updated_at) {
				$deal = $dealPosition->deal;
				$createdStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
				if ($deal->status_id == $createdStatus->id) {
					$inWorkStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::IN_WORK_STATUS);
					if ($inWorkStatus) {
						$deal->status_id = $inWorkStatus->id;
						$deal->save();
					}
				}
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
		return $this->belongsTo(Score::class, 'id', 'deal_position_id');
	}
	
	public function bills()
	{
		return $this->belongsToMany(Bill::class, 'bills_positions', 'deal_position_id', 'bill_id')
			->using(BillPosition::class)
			->withTimestamps();
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
	
	/**
	 * @return int
	 */
	public function billPayedAmount()
	{
		$amount = 0;
		foreach ($this->bills as $bill) {
			$status = $bill->status;
			if (!$status) continue;
			if ($bill->status->alias != Bill::PAYED_STATUS) continue;
			
			$amount += $bill->amount;
		}
		
		return $amount;
	}
	
	/**
	 * @return int
	 */
	public function amount()
	{
		$amount = $this->amount - $this->aeroflotBonusWriteOffAmount();
		
		return $amount;
	}
	
	/**
	 * Скидка после списания милей "Аэрофлот Бонус"
	 *
	 * @return int
	 */
	public function aeroflotBonusWriteOffAmount()
	{
		$amount = 0;
		foreach ($this->bills as $bill) {
			$aeroflotBonusAmount = ($bill->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER && $bill->aeroflot_state == AeroflotBonusService::PAYED_STATE) ? $bill->aeroflot_bonus_amount : 0;
			
			$amount += $aeroflotBonusAmount;
		}
		
		return $amount;
	}
	
	/**
	 * @return int
	 */
	public function balance()
	{
		if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
			\Log::debug('label 4.0: - ' . $this->billPayedAmount() . ' - ' . $this->amount());
		}
		return $this->billPayedAmount() - $this->amount();
	}
}
