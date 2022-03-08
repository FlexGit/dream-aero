<?php

namespace App\Models;

use App\Models\City;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name наименование продукта
 * @property string $alias алиас
 * @property int $product_type_id тип продукта
 * @property int $user_id пользователь
 * @property int $duration длительность полёта, мин.
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\City[] $cities
 * @property-read int|null $cities_count
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\ProductType|null $productType
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 * @property int $employee_id пилот
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEmployeeId($value)
 */
class Product extends Model
{
    use HasFactory, SoftDeletes/*, RevisionableTrait*/;
	
	/*const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'product_type_id' => 'Тип продукта',
		'user_id' => 'Пользователь',
		'city_id' => 'Город',
		'duration' => 'Длительность',
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
		'product_type_id',
		'user_id',
		'duration',
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
	
	public function productType()
	{
		return $this->hasOne(ProductType::class, 'id', 'product_type_id');
	}
	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
	
	public function cities()
	{
		return $this->belongsToMany(City::class, 'cities_products', 'product_id', 'city_id')
			->using(CityProduct::class)
			->withPivot(['price', 'currency_id', 'discount_id', 'is_hit', 'score', 'is_active', 'data_json'])
			->withTimestamps();
	}
	
	/**
	 * @param int $cityId
	 * @param null $contractorId
	 * @return array
	 */
	public function format($cityId = 1, $contractorId = null)
	{
		$cityProduct = $this->cities()
			->where('cities_products.is_active', true)
			->find($cityId);
		if (!$cityProduct || !$cityProduct->pivot) return [];
		
		/*$price = $cityProduct->pivot->price;
		if ($cityProduct->pivot->discount) {
			$price = $cityProduct->pivot->discount->is_fixed ? ($price - $cityProduct->pivot->discount->value) : ($price - $price * $cityProduct->pivot->discount->value / 100);
		}*/
		$price = $this->calcAmount($contractorId, $cityId);

		//$data = $this->data_json ?? [];
		$pivotData = json_decode($cityProduct->pivot->data_json, true);

		return [
			'id' => $this->id,
			'name' => $this->name,
			'alias' => $this->alias,
			'duration' => $this->duration,
			'price' => round($price),
			'currency' => $cityProduct->pivot->currency ? $cityProduct->pivot->currency->name : 'руб',
			'is_hit' => (bool)$cityProduct->pivot->is_hit,
			'is_unified' => in_array($this->productType->alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]),
			'icon_file_path' => (is_array($this->data_json) && array_key_exists('icon_file_path', $this->data_json)) ? \URL::to('/upload/' . $this->data_json['icon_file_path']) : null,
			'is_booking_allow' => (array_key_exists('is_booking_allow', $pivotData) && $pivotData['is_booking_allow']) ? true : false,
			'is_certificate_purchase_allow' => (array_key_exists('is_certificate_purchase_allow', $pivotData) && $pivotData['is_certificate_purchase_allow']) ? true : false,
			'tariff_type' => $this->productType ? $this->productType->format() : null,
			'user' => $this->user ? $this->user->format() : null,
			/*'city' => $this->city ? $this->city->format() : null,*/
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
		
		// для тарифа, действующего в любом городе
		if ($isUnified && in_array($alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS])) {
			$city = HelpFunctions::getEntityByAlias(City::class, City::MSK_ALIAS);
			
			$product = Product::where('product_type_id', $productTypeId)
				->whereIn('city_id', [$city->id, 0])
				->where('is_active', true)
				->orderByDesc('city_id')
				->first();
		}
		
		// базовая цена тарифа
		$price = $product->price ?? 0;
		if (!$price) return 0;

		// скидка по продукту
		if ($product->discount) {
			if ($product->discount->is_fixed) {
				$price = round($price - $product->discount->value);
			} else {
				$price = round($price - $price * $product->discount->value / 100);
			}
		};

		// персональная скидка контрагента (по времени налета)
		if ($contractor->discount) {
			if ($contractor->discount->is_fixed) {
				$price = round($price - $contractor->discount->value);
			} else {
				$price = round($price - $price * $contractor->discount->value / 100);
			}
		}
		
		// скидка по промокоду
		if ($promocode instanceof Promocode && $promocode->discount) {
			if ($promocode->discount->is_fixed) {
				$price = round($price - $promocode->discount->value);
			} else {
				$price = round($price - $price * $promocode->discount->value / 100);
			}
		}
		
		$date = date('Y-m-d');
		
		// скидка по акции
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
		//if (in_array($weekDay, [6, 7]) && $alias == ProductType::ULTIMATE_ALIAS) return true;
		
		// Остальные тарифы доступны для заказа в любые дни
		if ($alias != ProductType::REGULAR_ALIAS) return true;
		
		return false;
	}

	/**
	 * @param $contractorId
	 * @param $cityId
	 * @param string $source
	 * @param bool $isFree
	 * @param int $locationId
	 * @param int $paymentMethodId
	 * @param int $promoId
	 * @param int $promocodeId
	 * @param int $certificateId
	 * @param bool $isUnified
	 * @param bool $isAirlineMilesPurchase
	 * @param int $score
	 *
	 * @return float|int
	 */
	public function calcAmount($contractorId, $cityId, $source = 'api', $isFree = false, $locationId = 0, $paymentMethodId = 0, $promoId = 0, $promocodeId = 0, $certificateId = 0, $isUnified = false, $isAirlineMilesPurchase = false, $score = 0)
	{
		if ($isFree) return 0;

		if ($certificateId && $locationId) {
			$date = date('Y-m-d');
			$location = Location::find($locationId);
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			// проверка сертификата на валидность
			$certificate = Certificate::whereIn('city_id', [$location->city->id, 0])
				->where('status_id', $certificateStatus->id)
				->where('product_id', $this->id)
				->where(function ($query) use ($date) {
					$query->where('expire_at', '>=', $date)
						->orWhereNull('expire_at');
				})->find($certificateId);
			if ($certificate) return 0;
		}

		$contractor = $contractorId ? Contractor::whereIsActive(true)->find($contractorId) : null;
		$promo = $promoId ? Promo::whereIsActive(true)->find($promoId) : null;
		/*$paymentMethod = $paymentMethodId ? PaymentMethod::whereIsActive(true)->find($paymentMethodId) : null;*/
		$promocode = $promocodeId ? Promocode::whereRelation('cities', 'cities.id', '=', $cityId)
			->find($promocodeId) : null;

		// если это покупка сертификата и город любой, то цены продуктов города Москва
		if ($isUnified && !$locationId) {
			$mskCity = HelpFunctions::getEntityByAlias(City::class, City::MSK_ALIAS);
			$cityId = $mskCity->id;
		}

		$cityProduct = $this->cities()->where('cities_products.is_active', true)->find($cityId);
		if (!$cityProduct || !$cityProduct->pivot) return 0;

		// базовая стоимость продукта
		$amount = $cityProduct->pivot->price;

		// если указаны баллы на списание (только для мобилки)
		if ($score > 0 && $source == Deal::MOB_SOURCE) {
			$amount -= $score;
			if ($amount <= 0) return 0;
		}

		// скидка на продукт
		$discount = $cityProduct->pivot->discount ?? null;
		if ($discount) {
			$amount = $discount->is_fixed ? ($amount - $discount->value) : ($amount - $amount * $discount->value / 100);

			return ($amount > 0) ? round($amount) : 0;
		}

		// скидка по промокоду/акции/клиента действует только для типов тарифов Regular и Ultimate
		if ($this->productType && in_array($this->productType->alias, [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS])) {
			// сидка по промокоду
			$discount = ($promocode && $promocode->discount) ? $promocode->discount : null;
			if ($discount) {
				$amount = $discount->is_fixed ? ($amount - $discount->value) : ($amount - $amount * $discount->value / 100);

				return ($amount > 0) ? round($amount) : 0;
			}

			// скидка по указанной акции
			$discount = ($promo && $promo->discount) ? $promo->discount : null;
			if ($discount) {
				$amount = $discount->is_fixed ? ($amount - $discount->value) : ($amount - $amount * $discount->value / 100);

				return ($amount > 0) ? round($amount) : 0;
			}

			$date = date('Y-m-d');
			// если есть активные акции для публикации, применяем наиболее позднюю по дате создания
			$promo = Promo::where('is_active', true)
				->where('is_published', true)
				->where('discount_id', '!=', 0)
				->whereIn('city_id', [$cityId, 0])
				->where(function ($query) use ($date) {
					$query->where('active_from_at', '<=', $date)
						->orWhereNull('active_from_at');
				})
				->where(function ($query) use ($date) {
					$query->where('active_to_at', '>=', $date)
						->orWhereNull('active_to_at');
				})
				->latest()->first();
			$discount = ($promo && $promo->discount) ? $promo->discount : null;
			if ($discount) {
				$amount = $discount->is_fixed ? ($amount - $discount->value) : ($amount - $amount * $discount->value / 100);

				return ($amount > 0) ? round($amount) : 0;
			}

			// скидка контрагента
			if ($contractor) {
				// все статусы контрагента
				$statuses = Status::where('is_active', true)
					->where('type', Status::STATUS_TYPE_CONTRACTOR)
					->get();

				// время налета контрагента
				$contractorFlightTime = $contractor->getFlightTime();

				// статус контрагента
				$status = $contractor->getStatus($statuses, $contractorFlightTime ?? 0);

				if ($status->discount) {
					$amount = $status->discount->is_fixed ? ($amount - $status->discount->value) : ($amount - $amount * $status->discount->value / 100);
				}
			}
		}

		return ($amount > 0) ? round($amount) : 0;
	}
}
