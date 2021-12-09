<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property string $name имя пользователя
 * @property string $comment текст отзыва
 * @property int $location_id локация, о которой отзыв
 * @property int $is_active признак активности
 * @property \datetime|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 */
class Review extends Model
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
		'comment',
		'location_id',
		'is_active',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:d.m.Y',
	];

	public function location() {
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}
}
