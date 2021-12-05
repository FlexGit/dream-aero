<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\EmployeePosition
 *
 * @property int $id
 * @property string $name имя
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmployeePosition extends Model
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
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
	];
}
