<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Deal
 *
 * @property int $id
 * @property string $number номер сделки
 * @property int $status_id статус сделки
 * @property int $contractor_id контрагент, с которым заключена сделка
 * @property int $duration продолжительность полета
 * @property int $order_id ссылка на заказ
 * @property int $certificate_id ссылка на сертификат
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property \datetime|null $flight_at дата и время полета
 * @property int $created_by_user_id пользователь, создавший сделку
 * @property int $updated_by_user_id пользователь, изменивший последним сделку
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\User|null $createdByUser
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\User|null $updatedByUser
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCreatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUpdatedByUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Score|null $score
 * @property-read \App\Models\Tariff|null $tariff
 */
class Deal extends Model
{
    use HasFactory, RevisionableTrait;
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'status_id',
		'contractor_id',
		'tariff_id',
		'duration',
		'order_id',
		'certificate_id',
		'city_id',
		'location_id',
		'flight_at',
		'invite_sent_at',
		'created_by_user_id',
		'updated_by_user_id',
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
		'flight_at' => 'datetime:Y-m-d H:i',
		'data_json' => 'array',
	];
	
	public function status() {
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}

	public function contractor() {
		return $this->hasOne('App\Models\Contractor', 'id', 'contractor_id');
	}

	public function city() {
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function location() {
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}

	public function tariff() {
		return $this->hasOne('App\Models\Tariff', 'id', 'tariff_id');
	}

	public function createdByUser() {
		return $this->hasOne('App\Models\User', 'id', 'created_by_user_id');
	}
	
	public function updatedByUser() {
		return $this->hasOne('App\Models\User', 'id', 'updated_by_user_id');
	}
}
