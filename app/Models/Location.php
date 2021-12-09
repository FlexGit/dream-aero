<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $name наименование локации
 * @property int $legal_entity_id юр.лицо, на которое оформлена локация
 * @property int $city_id город, в котором находится локация
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FlightSimulator[] $simulator
 * @property-read int|null $simulator_count
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\LegalEntity $legalEntity
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLegalEntityId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employee[] $employee
 * @property-read int|null $employee_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 */
class Location extends Model
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
		'name',
		'city_id',
		'data_json',
		'is_active',
		'legal_entity_id'
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];

	public function city() {
		return $this->belongsTo('App\Models\City', 'city_id', 'id');
	}
	
	public function legalEntity() {
		return $this->belongsTo('App\Models\LegalEntity', 'legal_entity_id', 'id');
	}

	public function simulator() {
		return $this->hasMany('App\Models\FlightSimulator', 'location_id', 'id');
	}
	
	public function employee() {
		return $this->hasMany('App\Models\Employee', 'location_id', 'id');
	}

	public function format() {
		$data = $this->data_json;

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
			'is_active' => $this->is_active,
			'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
			'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
		];
	}
}
