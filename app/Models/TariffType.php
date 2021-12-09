<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\TariffType
 *
 * @property int $id
 * @property string $name наименование тарифа
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType query()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tariff[] $tariffs
 * @property-read int|null $tariffs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 */
class TariffType extends Model {
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
		'is_active',
		'data_json'
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
	
	public function tariffs() {
		return $this->hasMany('App\Models\Tariff', 'tariff_type_id', 'id');
	}
}
