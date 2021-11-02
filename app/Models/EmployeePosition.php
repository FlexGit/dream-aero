<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
	];
}
