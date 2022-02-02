<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

/**
 * App\Models\FlightSimulator
 *
 * @property int $id
 * @property string $name наименование авиатренажера
 * @property string $alias алиас
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newQuery()
 * @method static \Illuminate\Database\Query\Builder|FlightSimulator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator query()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FlightSimulator withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FlightSimulator withoutTrashed()
 * @mixin \Eloquent
 */
class FlightSimulator extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;
	
	const ALIAS_737 = '737NG';
	const ALIAS_A320 = 'A320';
	
	/*const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'is_active' => 'Признак активности',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;*/
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'alias',
		'is_active',
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
		'is_active' => 'boolean',
	];
	
	public function locations()
	{
		return $this->belongsToMany(Location::class, 'locations_flight_simulators', 'flight_simulator_id', 'location_id')
			->withPivot('data_json')
			->withTimestamps();
	}

	public function format()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
