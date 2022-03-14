<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Deal
 *
 * @property int $id
 * @property string|null $number номер
 * @property int $status_id статус
 * @property int $contractor_id контрагент
 * @property string $name имя
 * @property string $phone номер телефона
 * @property string $email e-mail
 * @property int $product_id продукт
 * @property int $certificate_id сертификат
 * @property int $duration продолжительность полета
 * @property int $amount стоимость
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property int $promo_id акция
 * @property int $promocode_id промокод
 * @property bool $is_certificate_purchase покупка сертификата
 * @property int $is_unified сертификат действует во всех городах
 * @property \datetime|null $flight_at дата и время полета
 * @property \datetime|null $invite_sent_at последняя дата отправки приглашения на e-mail
 * @property \datetime|null $certificate_sent_at последняя дата отправки сертификата на e-mail
 * @property string|null $source источник
 * @property int $user_id пользователь
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor $contractor
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Promo|null $promo
 * @property-read \App\Models\Promocode|null $promocode
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newQuery()
 * @method static \Illuminate\Database\Query\Builder|Deal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCertificateSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereInviteSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereIsCertificatePurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereIsUnified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal wherePromoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal wherePromocodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Deal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Deal withoutTrashed()
 * @mixin \Eloquent
 * @property int $flight_simulator_id
 * @property-read \App\Models\FlightSimulator|null $simulator
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereFlightSimulatorId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DealPosition[] $positions
 * @property-read int|null $positions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $scores
 * @property-read int|null $scores_count
 */
class Deal extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Нмер',
		'status_id' => 'Статус',
		'contractor_id' => 'Контрагент',
		'name' => 'Имя',
		'phone' => 'Телефон',
		'email' => 'E-mail',
		'source' => 'Источник',
		'user_id' => 'Пользователь',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
		'comment' => 'Комментарий',
	];
	
	const CREATED_STATUS = 'deal_created';
	const CONFIRMED_STATUS = 'deal_confirmed';
	const PAUSED_STATUS = 'deal_paused';
	const RETURNED_STATUS = 'deal_returned';
	const CANCELED_STATUS = 'deal_canceled';
	const STATUSES = [
		self::CREATED_STATUS,
		self::CONFIRMED_STATUS,
		self::PAUSED_STATUS,
		self::RETURNED_STATUS,
		self::CANCELED_STATUS,
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
		'status_id',
		'contractor_id',
		'name',
		'phone',
		'email',
		'user_id',
		'source',
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
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();
		
		Deal::created(function (Deal $deal) {
			$deal->number = $deal->generateNumber();
			$deal->save();
		});

		Deal::saved(function (Deal $deal) {
			if (!$deal->user_id && $deal->source == Deal::ADMIN_SOURCE) {
				$deal->user_id = \Auth::user()->id;
				$deal->save();
			}
		});

		Deal::deleting(function(Deal $deal) {
			$deal->positions()->delete();
			$deal->bills()->delete();
			$deal->events()->delete();
		});
	}
	
	public function contractor()
	{
		return $this->hasOne(Contractor::class, 'id', 'contractor_id');
	}

	public function positions()
	{
		return $this->hasMany(DealPosition::class);
	}

	public function bills()
	{
		return $this->hasMany(Bill::class, 'deal_id', 'id');
	}

	public function status()
	{
		return $this->hasOne(Status::class, 'id', 'status_id');
	}
	
	public function events()
	{
		return $this->hasMany(Event::class, 'deal_id', 'id');
	}
	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	public function scores()
	{
		return $this->hasMany(Score::class, 'deal_id', 'id');
	}

	/**
	 * @return string
	 */
	public function generateNumber()
	{
		/*$locationCount = $this->city ? $this->city->locations->count() : 0;

		$cityAlias = $this->city ? mb_strtolower($this->city->alias) : '';
		$locationAlias = $this->location ? $this->location->alias : '';

		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';

		$alias = ($locationCount > 1 && $this->location) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);*/
		
		return 'D' . date('y') . /*$alias . $productTypeAlias . $productDuration .*/ sprintf('%05d', $this->id);
	}
	
	/**
	 * @return array
	 */
	public function format()
	{
		//$data = $this->data_json ?? [];
		
		return [
			'id' => $this->id,
			'number' => $this->number,
			'status' => $this->status ? $this->status->name : null,
		];
	}

	public function amount() {
		$amount = 0;
		foreach ($this->positions ?? [] as $position) {
			if ($position->certificate && $position->certificate->status && in_array($position->certificate->status->alias, [Certificate::CANCELED_STATUS, Certificate::RETURNED_STATUS])) continue;

			$amount += $position->amount;
		}

		/*$scoreAmount = 0;
		if ($this->scores) {
			foreach ($this->scores ?? [] as $score) {
				if ($score->type != Score::USED_TYPE) continue;

				$scoreAmount += abs($score->score);
			}
		}*/

		return /*(*/$amount/* - $scoreAmount)*/;
	}

	public function billPayedAmount()
	{
		$amount = 0;
		foreach ($this->bills ?? [] as $bill) {
			if ($bill->status->alias != Bill::PAYED_STATUS) continue;

			$amount += $bill->amount;
		}

		return $amount;
	}

	public function balance()
	{
		return $this->billPayedAmount() - $this->amount();
	}
}
