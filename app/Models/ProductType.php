<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

/**
 * App\Models\ProductType
 *
 * @property int $id
 * @property string $name наименование типа продукта
 * @property string $alias алиас
 * @property bool $is_tariff является ли продукт тарифом
 * @property int $sort сортировка
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProductType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereIsTariff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProductType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductType withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $version
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereVersion($value)
 */
class ProductType extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;
	
    const DURATIONS = [
    	30,
		60,
		90,
		120,
		150,
		180,
		360,
		540,
	];
    
    const REGULAR_ALIAS = 'regular';
	const ULTIMATE_ALIAS = 'ultimate';
	const COURSES_ALIAS = 'courses';
	const PLATINUM_ALIAS = 'platinum';
	const VIP_ALIAS = 'vip';
	const SERVICES_ALIAS = 'services';
	const REGULAR_EXTRA_ALIAS = 'regular_extra';
	const ULTIMATE_EXTRA_ALIAS = 'ultimate_extra';

	const RU_VERSION = 'ru';
	const EN_VERSION = 'en';
	const VERSIONS = [
		self::RU_VERSION,
		self::EN_VERSION,
	];
    
    /*const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'is_tariff' => 'Является тарифом',
		'version' => 'Версия',
		'is_active' => 'Активность',
		'data_json' => 'Дополнительная информация',
		'duration' => 'Длительность',
		'with_user' => 'С пилотом',
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
		'name',
		'alias',
		'is_tariff',
		'version',
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
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'is_tariff' => 'boolean',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];
	
	public function products()
	{
		return $this->hasMany(Product::class, 'product_type_id', 'id')
			->orderBy('product_type_id')
			->orderBy('duration');
	}
	
	/**
	 * @return array
	 */
	public function format()
	{
		$data = $this->data_json ?? [];
		
		return [
			'id' => $this->id,
			'name' => $this->name,
			'alias' => $this->alias,
			'description' => array_key_exists('description', $data) ? $data['description'] : null,
		];
	}
}
