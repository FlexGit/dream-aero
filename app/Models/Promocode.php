<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

/**
 * App\Models\Promocode
 *
 * @property int $id
 * @property string $number промокод
 * @property int $discount_id скидка
 * @property bool $is_active признак активности
 * @property \datetime|null $active_from_at дата начала активности
 * @property \datetime|null $active_to_at дата окончания активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\City[] $cities
 * @property-read int|null $cities_count
 * @property-read \App\Models\Discount|null $discount
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newQuery()
 * @method static \Illuminate\Database\Query\Builder|Promocode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveFromAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveToAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Promocode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Promocode withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $uuid uuid
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereUuid($value)
 * @property string|null $type
 * @property int $contractor_id
 * @property int $location_id
 * @property int $flight_simulator_id
 * @property \datetime|null $sent_at
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contractor[] $contractors
 * @property-read int|null $contractors_count
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereType($value)
 * @property-read \App\Models\Location|null $location
 */
class Promocode extends Model
{
    use HasFactory, SoftDeletes/*, RevisionableTrait*/;
	
    /*const ATTRIBUTES = [
		'number' => 'Номер',
		'discount_id' => 'Скидка',
		'active_from_at' => 'Начало активности',
		'active_to_at' => 'Окончание активности',
		'is_active' => 'Активность',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;*/
	
    const SIMULATOR_TYPE = 'simulator';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'type',
		'contractor_id',
		'location_id',
		'flight_simulator_id',
		'discount_id',
		'is_active',
		'active_from_at',
		'active_to_at',
		'sent_at',
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
		'active_from_at' => 'datetime:Y-m-d H:i:s',
		'active_to_at' => 'datetime:Y-m-d H:i:s',
		'sent_at' => 'datetime:Y-m-d H:i:s',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];

	public static function boot()
	{
		parent::boot();

		Promocode::created(function (Promocode $promocode) {
			$promocode->uuid = $promocode->generateUuid();
			$promocode->save();
		});
	}

	public function discount()
	{
		return $this->hasOne(Discount::class, 'id', 'discount_id');
	}
	
	public function cities()
	{
		return $this->belongsToMany(City::class, 'cities_promocodes', 'promocode_id', 'city_id')
			->using(CityPromocode::class)
			->withTimestamps();
	}
	
	public function contractor()
	{
		return $this->hasOne(Contractor::class, 'id', 'contractor_id');
	}
	
	public function location()
	{
		return $this->hasOne(Location::class, 'id', 'location_id');
	}
	
	public function contractors()
	{
		return $this->belongsToMany(Contractor::class, 'contractors_promocodes', 'promocode_id', 'contractor_id')
			->using(ContractorPromocode::class)
			->withTimestamps();
	}
	
	public function format() {
		$data = $this->data_json ?? [];
		
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}

	public function valueFormatted() {
		$value = $this->number;
		$value .= $this->discount ? ' (-' . $this->discount->valueFormatted() . ')' : '';

		return  $value;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function generateUuid()
	{
		return (string)\Webpatser\Uuid\Uuid::generate();
	}
}
