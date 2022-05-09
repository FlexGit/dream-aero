<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Contractor
 *
 * @property int $id
 * @property string $name имя
 * @property string|null $lastname фамилия
 * @property \datetime|null $birthdate дата рождения
 * @property string|null $phone основной номер телефона
 * @property string $email основной e-mail
 * @property string|null $password пароль в md5
 * @property string|null $remember_token
 * @property int $city_id город, к которому привязан контрагент
 * @property int $discount_id скидка
 * @property int $user_id пользователь
 * @property bool $is_active признак активности
 * @property \datetime|null $last_auth_at дата последней по времени авторизации
 * @property string|null $source источник
 * @property string|null $uuid uuid
 * @property bool $is_subscribed подписан на рассылку
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Discount|null $discount
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newQuery()
 * @method static \Illuminate\Database\Query\Builder|Contractor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereIsSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastAuthAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Contractor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Contractor withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Promocode[] $contractorPromocodes
 * @property-read int|null $contractor_promocodes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Promocode[] $promocodes
 * @property-read int|null $promocodes_count
 */
class Contractor extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'name' => 'Имя',
		'lastname' => 'Фамилия',
		'birthdate' => 'Дата рождения',
		'phone' => 'Телефон',
		'email' => 'E-mail',
		'password' => 'Пароль',
		'city_id' => 'Город',
		'discount_id' => 'Скидка',
		'source' => 'Источник',
		'data_json' => 'Дополнительная информация',
		'is_active' => 'Признак активности',
		'last_auth_at' => 'Последняя авторизация',
		'user_id' => 'Пользователь',
		'uuid' => 'Uuid',
		'is_subscribed' => 'Подписан ра рассылку',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	CONST RESEND_CODE_INTERVAL = 25;
	CONST CODE_TTL = 5 * 60;
	
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

	const REGISTRATION_SCORE = 500;
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	protected $dontKeepRevisionOf = ['password', 'source', 'data_json', 'last_auth_at', 'uuid'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'lastname',
		'birthdate',
		'phone',
		'email',
		'password',
		'city_id',
		'discount_id',
		'source',
		'data_json',
		'is_active',
		'last_auth_at',
		'user_id',
		'uuid',
		'is_subscribed',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
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
		'email_verified_at' => 'datetime',
		'last_auth_at' => 'datetime:Y-m-d H:i:s',
		'birthdate' => 'datetime:Y-m-d',
		'is_active' => 'boolean',
		'data_json' => 'array',
		'is_subscribed' => 'boolean',
	];
	
	public static function boot()
	{
		parent::boot();
		
		Contractor::created(function (Contractor $contractor) {
			$contractor->uuid = $contractor->generateUuid();
			$contractor->save();
		});

		Contractor::deleting(function (Contractor $contractor) {
			$contractor->tokens()->delete();
		});
	}
	
	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}
	
	public function discount()
	{
		return $this->hasOne(Discount::class, 'id', 'discount_id');
	}
	
	public function tokens()
	{
		return $this->hasMany(Token::class, 'contractor_id', 'id');
	}
	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
	
	public function contractorPromocodes()
	{
		return $this->belongsToMany(Promocode::class, 'contractor_promocodes', 'promocode_id', 'contractor_id')
			->using(ContractorPromocode::class)
			->withPivot(['was_used'])
			->withTimestamps();
	}
	
	public function promocodes()
	{
		return $this->belongsToMany(Promocode::class, 'cities_promocodes', 'city_id', 'promocode_id')
			->using(CityPromocode::class)
			->withTimestamps();
	}
	
	public function notifications()
	{
		return $this->belongsToMany(Notification::class, 'notifications_contractors', 'contractor_id', 'notification_id')
			->using(NotificationContractor::class)
			->withPivot(['is_new'])
			->withTimestamps();
	}
	
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function generateUuid()
	{
		return (string)\Webpatser\Uuid\Uuid::generate();
	}
	
	public function format()
	{
		$data = $this->data_json ? (is_array($this->data_json) ? $this->data_json : json_decode($this->data_json, true)) : [];

		$avatar = isset($data['avatar']) ? $data['avatar'] : null;
		$avatarFileName = ($avatar && isset($avatar['name'])) ? $avatar['name'] : null;
		$avatarFileExt = ($avatar && isset($avatar['ext'])) ? $avatar['ext'] : null;

		$base64 = '';
		if ($avatarFileName && $avatarFileExt && Storage::disk('private')->exists('contractor/avatar/' . $avatarFileName . '.' . $avatarFileExt)) {
			$file = storage_path('app/private/contractor/avatar/' . $avatarFileName . '.' . $avatarFileExt);
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$fileData = file_get_contents($file);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
		}

		// все статусы контрагента
		$statuses = Status::where('is_active', true)
			->where('type', Status::STATUS_TYPE_CONTRACTOR)
			->get();
		
		// время налета контрагента
		$contractorFlightTime = $this->getFlightTime();
		// баллы контрагента
		$score = $this->getScore();
		// статус контрагента
		$status = $this->getStatus($statuses, $contractorFlightTime ?? 0);

		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'name' => $this->name,
			'lastname' => $this->lastname,
			'email' => $this->email,
			'phone' => $this->phone,
			'city' => $this->city ? $this->city->format() : null,
			'birthdate' => $this->birthdate ? $this->birthdate->format('Y-m-d') : null,
			'avatar_file_base64' => $base64 ?: null,
			'score' => $score ?? 0,
			'status' => $status->name ?? null,
			'flight_time' => (int)$contractorFlightTime,
			'discount' => $status->discount ? $status->discount->valueFormatted() : '0%',
			/*'is_new' => $this->password ? true : false,*/
		];
	}
	
	/**
	 * @param $statuses
	 * @param int $contractorFlightTime
	 * @return mixed|null
	 */
	public function getStatus($statuses, $contractorFlightTime = 0)
	{
		$flightTimes = [];
		foreach ($statuses ?? [] as $status) {
			if ($status->type != Status::STATUS_TYPE_CONTRACTOR) continue;
			
			$flightTimes[$status->id] = $status->flight_time;
		}
		if (!$flightTimes) return null;
		
		rsort($flightTimes);
		$result = array_filter($flightTimes, function($item) use ($contractorFlightTime) {
			return $item <= $contractorFlightTime;
		});
		if (!$result) {
			$result[] = $flightTimes[count($flightTimes) - 1];
		};

		$flightTime = array_shift($result);

		$statusId = 0;
		foreach ($statuses ?? [] as $status) {
			if ($status->type != Status::STATUS_TYPE_CONTRACTOR) continue;

			if ($status->flight_time == $flightTime) {
				$statusId = $status->id;
				break;
			}
		}
		foreach ($statuses ?? [] as $status) {
			if ($status->id == $statusId) {
				return $status;
			}
		}
		
		return null;
	}
	
	/**
	 * @return mixed
	 */
	public function getScore()
	{
		return Score::where('contractor_id', $this->id)
			->sum('score');
	}
	
	/**
	 * @return mixed
	 */
	public function getFlightTime()
	{
		/*return Event::where('event_type', Event::EVENT_SOURCE_DEAL)
			->where('stop_at', '<', Carbon::now()->addHour()->format('Y-m-d H:i:s'))
			->whereRelation('deal', 'contractor_id', '=', $this->id)
			->sum(DB::raw('TIMESTAMPDIFF(minute, start_at, stop_at)'));*/
		return Score::where('contractor_id', $this->id)
			->sum('duration');
	}
	
	/**
	 * @return mixed
	 */
	public function getFlightCount()
	{
		/*return Event::where('event_type', Event::EVENT_SOURCE_DEAL)
			->where('stop_at', '<', Carbon::now()->addHour()->format('Y-m-d H:i:s'))
			->whereRelation('deal', 'contractor_id', '=', $this->id)
			->count();*/

		return Score::where('contractor_id', $this->id)
			->where('duration', '>', 0)
			->count();
	}
	
	/**
	 * @param $statuses
	 * @return int|mixed
	 */
	public function getBalance($statuses)
	{
		$dealReturnedStatusId = $dealCanceledStatusId = $billPayedStatusId = $certificateReturnedStatus = $certificateCanceledStatus = 0;
		foreach ($statuses ?? [] as $status) {
			if ($status->type == Status::STATUS_TYPE_DEAL && $status->alias == Deal::RETURNED_STATUS) {
				$dealReturnedStatusId = $status->id;
			}
			if ($status->type == Status::STATUS_TYPE_DEAL && $status->alias == Deal::CANCELED_STATUS) {
				$dealCanceledStatusId = $status->id;
			}
			if ($status->type == Status::STATUS_TYPE_BILL && $status->alias == Bill::PAYED_STATUS) {
				$billPayedStatusId = $status->id;
			}
			if ($status->type == Status::STATUS_TYPE_CERTIFICATE && $status->alias == Certificate::RETURNED_STATUS) {
				$certificateReturnedStatus = $status->id;
			}
			if ($status->type == Status::STATUS_TYPE_CERTIFICATE && $status->alias == Certificate::CANCELED_STATUS) {
				$certificateCanceledStatus = $status->id;
			}
		}
		if (!$dealReturnedStatusId || !$dealCanceledStatusId || !$billPayedStatusId) return 0;

		$dealSum = 0;
		$deals = Deal::whereNotIn('status_id', [$dealReturnedStatusId, $dealCanceledStatusId])
			->where('contractor_id', $this->id)
			->orWhereRelation('positions', function ($query) use ($certificateReturnedStatus, $certificateCanceledStatus) {
				return $query->orWhereHas('certificate', function ($query) use ($certificateReturnedStatus, $certificateCanceledStatus) {
					return $query->whereNotIn('certificates.status_id', [$certificateReturnedStatus, $certificateCanceledStatus]);
				});
			})
			->withSum('positions as amount', 'amount')
			->get();
		foreach ($deals as $deal) {
			$dealSum += $deal->amount;
		}

		$billSum = Bill::where('status_id', $billPayedStatusId)
			->where('contractor_id', $this->id)
			->sum('amount');

		\Log::debug($this->id . ' - ' . $dealSum . ' - ' . $billSum);
		
		return ($billSum - $dealSum);
	}

	/**
	 * @return string
	 */
	public function fio()
	{
		return trim(($this->lastname ?? '') . ' ' . ($this->name ?? ''));
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
