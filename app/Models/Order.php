<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $number номер заказа (бронирования/сертификата)
 * @property int $status_id статус заказа
 * @property int $contractor_id контрагент, совершивший заказ
 * @property int $tariff_id тариф
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property string|null $filght_at дата и время полета
 * @property \datetime|null $invite_sent_at последняя дата отправки приглашения на e-mail
 * @property \datetime|null $certificate_expire_at срок окончания действия сертификата
 * @property \datetime|null $certificate_sent_at последняя дата отправки сертификата на e-mail
 * @property int $created_by_user_id пользователь, создавший заказ
 * @property int $updated_by_user_id пользователь, изменивший последним заказ
 * @property array|null $data_json дополнительная информация: комментарий к бронированию, имя получателя сертификата, адрес доставки сертификата, комментарий по доставке сертификата
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\User|null $createdByUser
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\Tariff|null $tariff
 * @property-read \App\Models\User|null $updatedByUser
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCertificateExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCertificateSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFilghtAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereInviteSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTariffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedByUserId($value)
 * @mixin \Eloquent
 * @property \datetime|null $flight_at дата и время полета
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFlightAt($value)
 * @property int $promocode_id
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromocodeId($value)
 * @property int $is_certificate_order заказ сертификата
 * @property string $certificate_number номер сертификата
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCertificateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsCertificateOrder($value)
 */
class Order extends Model
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
		'city_id',
		'location_id',
		'flight_at',
		'invite_sent_at',
		'certificate_expire_at',
		'certificate_sent_at',
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
		'invite_sent_at' => 'datetime:Y-m-d H:i:s',
		'certificate_expire_at' => 'datetime:Y-m-d H:i:s',
		'certificate_sent_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
	];
	
	public function status() {
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}

	public function contractor() {
		return $this->hasOne('App\Models\Contractor', 'id', 'contractor_id');
	}

	public function tariff() {
		return $this->hasOne('App\Models\Tariff', 'id', 'tariff_id');
	}
	
	public function city() {
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function location() {
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}
	
	public function createdByUser() {
		return $this->hasOne('App\Models\User', 'id', 'created_by_user_id');
	}
	
	public function updatedByUser() {
		return $this->hasOne('App\Models\User', 'id', 'updated_by_user_id');
	}
}
