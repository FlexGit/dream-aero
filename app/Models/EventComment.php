<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\EventComment
 *
 * @property int $id
 * @property string $name комментарий
 * @property int $event_id событие
 * @property int $created_by кто создал
 * @property int $updated_by кто изменил
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|EventComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventComment withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User|null $createdUser
 * @property-read \App\Models\User|null $updatedUser
 */
class EventComment extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'name' => 'Комментарий',
		'event_id' => 'Событие',
		'created_by' => 'Кто создал',
		'updated_by' => 'Кто изменил',
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
		'event_id',
		'created_by',
		'updated_by',
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
	];

	public function event()
	{
		return $this->belongsTo(Event::class, 'event_id', 'id');
	}
	
	public function createdUser()
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}
	
	public function updatedUser()
	{
		return $this->belongsTo(User::class, 'updated_by', 'id');
	}
}
