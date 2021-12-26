<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use App\Services\HelpFunctions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
 * @property \App\Models\Discount|null $discount скидка
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $last_auth_at дата последней по времени авторизации
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newQuery()
 * @method static \Illuminate\Database\Query\Builder|Contractor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastAuthAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Contractor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Contractor withoutTrashed()
 * @mixin \Eloquent
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
		'data_json' => 'Дополнительная информация',
		'is_active' => 'Признак активности',
		'last_auth_at' => 'Последняя авторизация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	CONST RESEND_CODE_INTERVAL = 25;
	CONST CODE_TTL = 5 * 60;
	
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
		'data_json',
		'is_active',
		'last_auth_at',
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
	];
	
	public static function boot()
	{
		parent::boot();
		
		Contractor::deleting(function (Contractor $contractor) {
			$contractor->tokens()->delete();
		});
	}
	
	public function city()
	{
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function discount()
	{
		return $this->hasOne('App\Models\Discount', 'id', 'discount_id');
	}
	
	public function tokens()
	{
		return $this->hasMany('App\Models\Token', 'contractor_id', 'id');
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

		$flightTime = DealPosition::where('status_id', '>', 0)
			->whereRelation('deal', 'contractor_id', '=', $this->id)
			->sum('duration');
		
		$score = Score::where('contractor_id', $this->id)
			->whereRelation('dealPosition', 'status_id', '>', 0)
			->sum('score');
		
		$status = HelpFunctions::getContractorStatus($flightTime ?? 0);

		if ($this->discount) {
			$discount = $this->discount->format();
		} else {
			$data = $status->data_json ?? [];
			$discount = [
				'value' => array_key_exists('discount', $data) ? $data['discount'] : 0,
				'is_fixed' => false,
			];
		}
		
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
			'flight_time' => $flightTime,
			'discount' => $discount ?? null,
			'is_new' => $this->password ? true : false,
		];
	}
}
