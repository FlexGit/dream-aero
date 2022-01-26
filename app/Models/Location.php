<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $name наименование локации
 * @property string $alias alias
 * @property int $legal_entity_id юр.лицо, на которое оформлена локация
 * @property int $city_id город, в котором находится локация
 * @property int $sort сортировка
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $user
 * @property-read int|null $user_count
 * @property-read \App\Models\LegalEntity $legalEntity
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FlightSimulator[] $simulator
 * @property-read int|null $simulator_count
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Query\Builder|Location onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLegalEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Location withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Location withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FlightSimulator[] $simulators
 * @property-read int|null $simulators_count
 */
class Location extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'legal_entity_id' => 'Юр. лицо',
		'city_id' => 'Город',
		'sort' => 'Сортировка',
		'data_json' => 'Дополнительная информация',
		'address' => 'Адрес',
		'working_ours' => 'Рабочие часы',
		'phone' => 'Телефон',
		'email' => 'E-mail',
		'map_link' => 'Ссылка на карту',
		'skype' => 'Skype',
		'whatsapp' => 'WhatsApp',
		'scheme_file_path' => 'Путь к файлу схемы',
		'is_active' => 'Признак активности',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
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
		'alias',
		'legal_entity_id',
		'city_id',
		'sort',
		'data_json',
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
		'data_json' => 'array',
	];

	public function city()
	{
		return $this->belongsTo(City::class, 'city_id', 'id');
	}
	
	public function legalEntity()
	{
		return $this->belongsTo(LegalEntity::class, 'legal_entity_id', 'id');
	}

	public function simulators()
	{
		return $this->belongsToMany(FlightSimulator::class, 'locations_flight_simulators', 'location_id', 'flight_simulator_id')
			->withPivot('data_json')
			->withTimestamps();
	}
	
	public function user()
	{
		return $this->hasMany(User::class, 'location_id', 'id');
	}
	
	public function format()
	{
		$data = $this->data_json ?? [];

		$simulators = [];
		foreach ($this->simulators ?? [] as $simulator) {
			$simulators[] = $simulator->format();
		}

		return [
			'id' => $this->id,
			'name' => $this->name,
			'address' => array_key_exists('address', $data) ? $data['address'] : null,
			'working_hours' => array_key_exists('working_hours', $data) ? $data['working_hours'] : null,
			'phone' => array_key_exists('phone', $data) ? $data['phone'] : null,
			'email' => array_key_exists('email', $data) ? $data['email'] : null,
			'skype' => array_key_exists('skype', $data) ? $data['skype'] : null,
			'whatsapp' => array_key_exists('whatsapp', $data) ? $data['whatsapp'] : null,
			'map_link' => array_key_exists('map_link', $data) ? $data['map_link'] : null,
			'scheme_file_path' => array_key_exists('scheme_file_path', $data) ? \URL::to('/upload/' . $data['scheme_file_path']) : null,
			'flight_simulators' => $simulators,
			/*'is_active' => $this->is_active,*/
			/*'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
			'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,*/
		];
	}
}
