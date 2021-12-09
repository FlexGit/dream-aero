<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string $name имя сотрудника
 * @property int $employee_position_id должность сотрудника
 * @property int $location_id локация сотрудника
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\EmployeePosition|null $position
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmployeePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 */
class Employee extends Model
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
		'employee_position_id',
		'location_id',
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
		'data_json' => 'array',
		'is_active' => 'boolean',
	];

	public function position() {
		return $this->hasOne('App\Models\EmployeePosition', 'id', 'employee_position_id');
	}

	public function location() {
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}
	
	public function format() {
		$data = $this->data_json ?? [];
		
		return [
			'id' => $this->id,
			'name' => $this->name,
			'photo_path' => (array_key_exists('photo_file_path', $data) && $data['photo_file_path']) ? \URL::to('/upload/' . $data['photo_file_path']) : null,
			'icon_path' => (array_key_exists('icon_file_path', $data) && $data['icon_file_path']) ? \URL::to('/upload/' . $data['icon_file_path']) : null,
			'instagram' => array_key_exists('instagram', $data) ? $data['instagram'] : null,
		];
	}
}
