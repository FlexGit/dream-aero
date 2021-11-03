<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\Contractor
 *
 * @property int $id
 * @property string $name имя
 * @property string $phone основной номер телефона
 * @property string $email основной e-mail
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int $city_id город, к которому привязан контрагент
 * @property array $data_json дополнительная информация
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
 */
class Contractor extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'phone',
		'email',
		'password',
		'city_id',
		'data_json',
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
		'data_json' => 'array',
		'last_auth_at' => 'datetime:Y-m-d H:i:s',
	];
}
