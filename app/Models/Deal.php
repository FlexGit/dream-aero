<?php

namespace App\Models;

use App\Services\AeroflotBonusService;
use App\Services\HelpFunctions;
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
 * @property int $city_id город, в котором будет осуществлен полет
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
 * @property string|null $uuid
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUuid($value)
 */
class Deal extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Нмер',
		'status_id' => 'Статус',
		'contractor_id' => 'Контрагент',
		'city_id' => 'Город',
		'name' => 'Имя',
		'phone' => 'Телефон',
		'email' => 'E-mail',
		'source' => 'Источник',
		'roistat' => 'Номер визита Roistat',
		'user_id' => 'Пользователь',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
		'comment' => 'Комментарий',
		'uuid' => 'Uuid',
	];
	
	const CREATED_STATUS = 'deal_created';
	const IN_WORK_STATUS = 'deal_in_work';
	const CONFIRMED_STATUS = 'deal_confirmed';
	const PAUSED_STATUS = 'deal_paused';
	const RETURNED_STATUS = 'deal_returned';
	const CANCELED_STATUS = 'deal_canceled';
	const STATUSES = [
		self::CREATED_STATUS,
		self::IN_WORK_STATUS,
		self::CONFIRMED_STATUS,
		self::PAUSED_STATUS,
		self::RETURNED_STATUS,
		self::CANCELED_STATUS,
	];

	const ADMIN_SOURCE = 'admin';
	const CALENDAR_SOURCE = 'calendar';
	const WEB_SOURCE = 'web';
	const MOB_SOURCE = 'api';
	const SOURCES = [
		self::ADMIN_SOURCE => 'Админка',
		self::CALENDAR_SOURCE => 'Календарь',
		self::WEB_SOURCE => 'Web',
		self::MOB_SOURCE => 'Mob',
	];
	
	const HOLIDAYS = [
		'02.05.2022',
		'03.05.2022',
		'09.05.2022',
		'10.05.2022',
		'13.06.2022',
		'04.11.2022',
		'02.01.2023',
		'03.01.2023',
		'04.01.2023',
		'05.01.2023',
		'06.01.2023',
		'23.02.2023',
		'24.02.2023',
		'08.03.2023',
		'01.05.2023',
		'08.05.2023',
		'09.05.2023',
		'12.06.2023',
		'06.11.2023',
		'01.01.2024',
		'02.01.2024',
		'03.01.2024',
		'04.01.2024',
		'05.01.2024',
		'23.02.2024',
		'08.03.2024',
		'01.05.2024',
		'02.05.2024',
		'03.05.2024',
		'09.05.2024',
		'10.05.2024',
		'12.06.2024',
		'04.11.2024',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	protected $dontKeepRevisionOf = ['source', 'uuid'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'status_id',
		'contractor_id',
		'city_id',
		'name',
		'phone',
		'email',
		'user_id',
		'source',
		'roistat',
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
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();
		
		Deal::created(function (Deal $deal) {
			$deal->number = $deal->generateNumber();
			$deal->uuid = (string)\Webpatser\Uuid\Uuid::generate();
			$deal->save();
			
			if ($deal->user_id) {
				$inWorkStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::IN_WORK_STATUS);
				if ($inWorkStatus) {
					$deal->status_id = $inWorkStatus->id;
					$deal->save();
				}
			}
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
	
	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
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
	
	/**
	 * @return int
	 */
	public function amount()
	{
		$amount = 0;
		foreach ($this->positions ?? [] as $position) {
			if ($position->certificate && $position->certificate->status && in_array($position->certificate->status->alias, [Certificate::CANCELED_STATUS, Certificate::RETURNED_STATUS])) continue;
			
			$amount += $position->amount;
		}
		
		$amount -= $this->aeroflotBonusWriteOffAmount();

		return $amount;
	}
	
	/**
	 * Скидка после списания милей "Аэрофлот Бонус" (по каждой позиции Сделки)
	 *
	 * @return int
	 */
	public function aeroflotBonusWriteOffAmount()
	{
		$amount = 0;
		foreach ($this->positions as $position) {
			$aeroflotBonusAmount = ($position->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER && $position->aeroflot_state == AeroflotBonusService::PAYED_STATE) ? $position->aeroflot_bonus_amount : 0;
			
			$amount += $aeroflotBonusAmount;
		}
		
		return $amount;
	}

	/**
	 * @return float|int
	 */
	public function scoreAmount()
	{
		$scoreAmount = 0;
		if ($this->scores) {
			foreach ($this->scores ?? [] as $score) {
				if ($score->type != Score::USED_TYPE) continue;
				
				$scoreAmount += abs($score->score);
			}
		}
		
		return $scoreAmount;
	}
	
	/**
	 * @return int
	 */
	public function billPayedAmount()
	{
		$amount = 0;
		foreach ($this->bills ?? [] as $bill) {
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
	public function balance()
	{
		return $this->billPayedAmount() - ($this->amount() - $this->scoreAmount());
	}
	
	/**
	 * @return string
	 */
	public function phoneFormatted()
	{
		$phoneCleared = preg_replace( '/[^0-9]/', '', $this->phone);
		return '+' . mb_substr($phoneCleared, 0, 1) . ' (' . mb_substr($phoneCleared, 1, 3) . ') ' . mb_substr($phoneCleared, 4, 3) . '-' . mb_substr($phoneCleared, 7, 2) . '-' . mb_substr($phoneCleared, 9, 2);
	}
}
