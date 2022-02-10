<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

class Rating extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;
	
	/*const ATTRIBUTES = [
		'name' => 'Наименование',
		'comment' => 'Комментарий',
		'city_id' => 'Город',
		'active_to_at' => 'Окончание активности',
		'is_active' => 'Активность',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;*/
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'value',
		'count',
		'content_id',
		'is_active',
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
		'is_active' => 'boolean',
	];

	public function content()
	{
		return $this->hasOne(Content::class, 'id', 'content_id');
	}
}
