<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name наименование
 * @property string $alias алиас
 * @property int $sort
 * @property int $is_active
 * @property array $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 */
class Status extends Model
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
		'alias',
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
		'data_json' => 'array',
	];
}
