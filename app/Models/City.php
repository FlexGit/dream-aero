<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

/**
 * App\Models\City
 *
 * @property int $id
 * @property string $name наименование
 * @property string $alias алиас
 * @property string|null $version версия сайта
 * @property string|null $timezone временная зона
 * @property int $sort сортировка
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация: часовой пояс
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Promocode[] $promocodes
 * @property-read int|null $promocodes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Query\Builder|City onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|City withTrashed()
 * @method static \Illuminate\Database\Query\Builder|City withoutTrashed()
 * @mixin \Eloquent
 */
class City extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;

	const MSK_ALIAS = 'msk';
	const SPB_ALIAS = 'spb';
	const VRN_ALIAS = 'vrn';
	const KZN_ALIAS = 'kzn';
	const KRD_ALIAS = 'krd';
	const NNV_ALIAS = 'nnv';
	const SAM_ALIAS = 'sam';
	const EKB_ALIAS = 'ekb';
	const NSK_ALIAS = 'nsk';
	const KHV_ALIAS = 'khv';
	/*const UAE_ALIAS = 'uae';*/
	const DC_ALIAS = 'dc';

	const RU_VERSION = 'ru';
	const EN_VERSION = 'en';
	const VERSIONS = [
		self::RU_VERSION,
		self::EN_VERSION,
	];

	/*const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'version' => 'Версия сайта',
		'timezone' => 'Временная зона',
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
		'name',
		'alias',
		'version',
		'timezone',
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
		'is_active' => 'boolean',
		'data_json' => 'array',
	];
	
	public function locations()
	{
		return $this->hasMany(Location::class, 'city_id', 'id');
	}
	
	public function products()
	{
		return $this->belongsToMany(Product::class, 'cities_products', 'city_id', 'product_id')
			->using(CityProduct::class)
			->withPivot(['price', 'currency_id', 'discount_id', 'is_hit', 'score', 'is_active', 'data_json'])
			->withTimestamps();
	}
	
	public function promocodes()
	{
		return $this->belongsToMany(Promocode::class, 'cities_promocodes', 'city_id', 'promocode_id')
			->using(CityPromocode::class)
			->withTimestamps();
	}

	/**
	 * @return array
	 */
	public function format()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
