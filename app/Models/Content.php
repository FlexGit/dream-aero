<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

class Content extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;

	const NEWS_TYPE = 'news';
	const REVIEWS_TYPE = 'reviews';
	const GALLERY_TYPE = 'gallery';

	const VERSION_RU = 'ru';
	const VERSION_AERO = 'aero';
	
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
		'title',
		'alias',
		'preview_text',
		'detail_text',
		'parent_id',
		'version',
		'meta_title',
		'meta_description',
		'is_active',
		'data_json',
		'published_at',
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
		'published_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
		'is_active' => 'boolean',
	];

	public function parent()
	{
		return $this->hasOne(Content::class, 'id', 'parent_id');
	}
}
