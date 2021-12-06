<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Contractor
 *
 * @property int $id
 * @property string $name имя
 * @property string $lastname имя
 * @property string $phone основной номер телефона
 * @property string $email основной e-mail
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int $city_id город, к которому привязан контрагент
 * @property int $discount скидка
 * @property \datetime|null $birthdate дата рождения
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \datetime|null $last_auth_at дата последней по времени авторизации
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastAuthAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\City|null $city
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereBirthdate($value)
 */
class Contractor extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, RevisionableTrait;
	
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
		'discount',
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
		'email_verified_at' => 'datetime',
		'last_auth_at' => 'datetime:Y-m-d H:i:s',
		'birthdate' => 'datetime:Y-m-d',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];
	
	public function city() {
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function format() {
		$data = $this->data_json ? json_decode($this->data_json, true) : [];
		
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
		
		return [
			'id' => $this->id,
			'name' => $this->name,
			'lastname' => $this->lastname,
			'email' => $this->email,
			'phone' => $this->phone,
			'city_id' => $this->city_id,
			'discount' => $this->discount,
			'birthdate' => $this->birthdate ? $this->birthdate->format('Y-m-d') : null,
			'avatar_file_base64' => $base64 ?: null,
			'flight_time' => null,
			'score' => null,
			'status' => null,
			'is_active' => $this->is_active,
			'is_new' => !$this->password ? true : false,
			'last_auth_at' => $this->last_auth_at ? $this->last_auth_at->format('Y-m-d H:i:s') : null,
			'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
			'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
		];
	}
}
