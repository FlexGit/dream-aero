<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name наименование продукта
 * @property string $alias алиас
 * @property int $product_type_id тип продукта
 * @property int $employee_id пилот
 * @property int $city_id город, в котором действует продукт
 * @property int $duration длительность полёта, мин.
 * @property int $price базовая цена продукта
 * @property bool $is_hit является ли продукт хитом продаж
 * @property bool $is_active признак активности
 * @property bool $is_unified сертификат действует на всех локациях
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Employee|null $employee
 * @property-read \App\Models\ProductType|null $productType
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsUnified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'product_type_id' => 'Тип продукта',
		'employee_id' => 'Пилот',
		'city_id' => 'Город',
		'duration' => 'Длительность',
		'price' => 'Стоимость',
		'is_hit' => 'Хит',
		'is_active' => 'Активность',
		'is_unified' => 'Единый тариф',
		'data_json' => 'Дополнительная информация',
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
		'alias',
		'product_type_id',
		'employee_id',
		'city_id',
		'duration',
		'price',
		'is_active',
		'is_hit',
		'is_unified',
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
		'is_hit' => 'boolean',
		'is_unified' => 'boolean',
	];
	
	public function productType()
	{
		return $this->hasOne('App\Models\ProductType', 'id', 'product_type_id');
	}
	
	public function employee()
	{
		return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
	}

	public function city()
	{
		return $this->hasOne('App\Models\City', 'id', 'city_id');
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
			'duration' => $this->duration,
			'price' => $this->price,
			'is_hit' => (bool)$this->is_hit,
			'is_unified' => (bool)$this->is_unified,
			'is_order_allow' => array_key_exists('is_order_allow', $data) ? (bool)$data['is_order_allow'] : false,
			'is_certificate_allow' => array_key_exists('is_certificate_allow', $data) ? (bool)$data['is_certificate_allow'] : false,
			'tariff_type' => $this->productType ? $this->productType->format() : null,
			'employee' => $this->employee ? $this->employee->format() : null,
			'city' => $this->city ? $this->city->format() : null,
		];
	}
	
	/**
	 * @param Contractor $contractor
	 * @param null $flightAt
	 * @param bool $isUnified
	 * @return float|int
	 */
	public function calculateProductPrice(Contractor $contractor, Promocode $promocode = null, $flightAt = null, $isUnified = false)
	{
		$product = $this;
		$productTypeId = $product->product_type_id ?? 0;
		$alias = $product->productType->alias ?? null;
		
		// для тарифа, действующего в любой локации
		if ($isUnified && in_array($alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS])) {
			$city = HelpFunctions::getCityByAlias(City::MSK_ALIAS);
			
			$product = Product::where('product_type_id', $productTypeId)
				->whereIn('city_id', [$city->id, 0])
				->where('is_active', true)
				->orderByDesc('city_id')
				->first();
		}
		
		// Todo счастливые часы
		
		
		// базовая цена тарифа
		$price = $product->price ?? 0;
		
		// персональная скидка контрагента
		if ($contractor->discount) {
			if ($contractor->discount->is_fixed) {
				$price = round($price - $contractor->discount->value);
			} else {
				$price = round($price - $price * $contractor->discount->value / 100);
			}
		}
		
		// скидка контрагента по налету
		
		// промокод
		if ($promocode instanceof Promocode && $promocode->discount) {
			if ($promocode->discount->is_fixed) {
				$price = round($price - $promocode->discount->value);
			} else {
				$price = round($price - $price * $promocode->discount->value / 100);
			}
		}
		
		$date = date('Y-m-d');
		
		// акции
		$promo = Promo::where('is_active', true)
			->where('active_from_at', '<=', $date)
			->where('active_to_at', '>=', $date)
			->first();
		if ($promo && $promo->discount) {
			if ($promo->discount->is_fixed) {
				$price = round($price - $promo->discount->value);
			} else {
				$price = round($price - $price * $promo->discount->value / 100);
			}
		}
		
		return $price;
	}
	
	/**
	 * @param $flightAt
	 * @return bool
	 */
	public function validateFlightDate($flightAt)
	{
		$alias = $this->productType->alias ?? null;
		if (!$alias) return false;
		
		$weekDay = date('N', strtotime($flightAt));
		
		// Regular доступен для заказа только в будни
		if (in_array($weekDay, [1, 2, 3, 4, 5]) && $alias == ProductType::REGULAR_ALIAS) return true;

		// Ultimate доступен для заказа только в выходные
		if (in_array($weekDay, [6, 7]) && $alias == ProductType::ULTIMATE_ALIAS) return true;
		
		// Остальные тарифы доступны для заказа в любые дни
		if (!in_array($alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS])) return true;
		
		return false;
	}
}
