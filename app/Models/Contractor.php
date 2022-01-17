<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

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
	const WEB_SOURCE = 'web';
	const MOB_SOURCE = 'api';
	const SOURCES = [
		self::ADMIN_SOURCE => 'Админка',
		self::WEB_SOURCE => 'Web',
		self::MOB_SOURCE => 'Mob',
	];
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
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
		$data = $this->data_json ?? [];
		
		$avatar = array_key_exists('avatar', $data) ? $data['avatar'] : null;
		$avatarFileName = ($avatar && array_key_exists('name', $avatar)) ? $avatar['name'] : null;
		$avatarFileExt = ($avatar && array_key_exists('ext', $avatar)) ? $avatar['ext'] : null;

		$base64 = '';
		if ($avatarFileName && $avatarFileExt && Storage::disk('private')->exists('contractor/avatar/' . $avatarFileName . '.' . $avatarFileExt)) {
			$file = storage_path('app/private/contractor/avatar/' . $avatarFileName . '.' . $avatarFileExt);
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$fileData = file_get_contents($file);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
		}

		// все статусы контрагента
		$statuses = Status::where('is_active', true)
			->where('type', 'contractor')
			->get();
		
		// время налета контрагента
		$contractorFlightTime = $this->getFlightTime();
		// баллы контрагента
		$score = $this->getScore();
		// статус контрагента
		$status = $this->getStatus($statuses, $contractorFlightTime ?? 0);

		return [
			'id' => $this->id,
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
			'discount' => $this->discount ? $this->discount->format() : null,
			'is_new' => $this->password ? true : false,
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
		
		krsort($flightTimes);
		$result = array_filter($flightTimes, function($item) use ($contractorFlightTime) {
			return $item <= $contractorFlightTime;
		});
		if (!$result) return null;
		
		$statusId = array_key_first($result);
		
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
		return Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->where('stop_at', '<', Carbon::now()->addHour()->format('Y-m-d H:i:s'))
			->whereRelation('deal', 'contractor_id', '=', $this->id)
			->sum(DB::raw('TIMESTAMPDIFF(minute, start_at, stop_at)'));
	}
	
	/**
	 * @return mixed
	 */
	public function getFlightCount()
	{
		return Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->where('stop_at', '<', Carbon::now()->addHour()->format('Y-m-d H:i:s'))
			->whereRelation('deal', 'contractor_id', '=', $this->id)
			->count();
	}
	
	/**
	 * @param $statuses
	 * @return int|mixed
	 */
	public function getBalance($statuses)
	{
		$dealReturnedStatusId = $dealCanceledStatusId = $billPayedStatusId = 0;
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
			
		}
		if (!$dealReturnedStatusId || !$dealCanceledStatusId || !$billPayedStatusId) return 0;
		
		$dealSum = Deal::whereNotIn('status_id', [$dealReturnedStatusId, $dealCanceledStatusId])
			->where('contractor_id', $this->id)
			->sum('amount');

		$billSum = Bill::where('status_id', $billPayedStatusId)
			->whereRelation('deals', 'contractor_id', '=', $this->id)
			->sum('amount');
		
		return ($billSum - $dealSum);
	}
}
