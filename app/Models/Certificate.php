<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Certificate
 *
 * @property int $id
 * @property string $number номер сертификата
 * @property int $status_id статус
 * @property int $contractor_id контрагент
 * @property int $product_id продукт
 * @property int $city_id город
 * @property \datetime|null $expire_at срок окончания действия сертификата
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Query\Builder|Certificate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Certificate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Certificate withoutTrashed()
 * @mixin \Eloquent
 * @property bool $is_unified сертификат действует во всех городах
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereIsUnified($value)
 */
class Certificate extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'status_id' => 'Статус',
		'contractor_id' => 'Контрагент',
		'product_id' => 'Продукт',
		'city_id' => 'Город',
		'expire_at' => 'Срок окончания действия',
		'is_unified' => 'Единый сертификат',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	const CREATED_STATUS = 'created';
	const REGISTERED_STATUS = 'registered';
	const RETURNED_STATUS = 'returned';
	const CANCELED_STATUS = 'canceled';
	const STATUSES = [
		self::CREATED_STATUS,
		self::REGISTERED_STATUS,
		self::RETURNED_STATUS,
		self::CANCELED_STATUS,
	];

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
		'product_id',
		'city_id',
		'expire_at',
		'is_unified',
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
		'expire_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
		'is_unified' => 'boolean',
	];
	
	public static function boot() {
		parent::boot();
		
		Certificate::created(function (Certificate $certificate) {
			$certificate->number = $certificate->generateNumber();
			$certificate->save();
		});
	}
	
	public function status()
	{
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}
	
	public function contractor()
	{
		return $this->hasOne('App\Models\Contractor', 'id', 'contractor_id');
	}
	
	public function product()
	{
		return $this->hasOne('App\Models\Product', 'id', 'product_id');
	}
	
	public function city()
	{
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$alias = $this->is_unified ? 'uni' : ($this->city ? mb_strtolower($this->city->alias) : '');
		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';
		
		return 'C' . date('y') . $alias . $productTypeAlias . $productDuration  . sprintf('%05d', $this->id);
	}
}
