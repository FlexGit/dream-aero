<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Score
 *
 * @property int $id
 * @property int $score количество баллов
 * @property int $contractor_id контрагент
 * @property int $event_id событие
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\Event|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Score newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Score newQuery()
 * @method static \Illuminate\Database\Query\Builder|Score onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Score query()
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Score withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Score withoutTrashed()
 * @mixin \Eloquent
 * @property int $duration
 * @property int $user_id
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereUserId($value)
 */
class Score extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'name' => 'Наименование',
		'contractor_id' => 'Контрагент',
		'event_id' => 'Сделка',
		'duration' => 'Длительность полета',
		'user_id' => 'Пользователь',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	const USED_TYPE = 'user';
	const SCORING_TYPE = 'scoring';

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'score',
		'contractor_id',
		'event_id',
		'duration',
		'user_id',
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
		'data_json' => 'array',
	];
	
	public function contractor()
	{
		return $this->hasOne(Contractor::class, 'id', 'contractor_id');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	public function event()
	{
		return $this->hasOne(Event::class, 'id', 'event_id');
	}
}
