<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property string|null $email E-mail
 * @property string|null $phone телефон
 * @method static \Illuminate\Database\Eloquent\Builder|City whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City wherePayAccountNumber($value)
 * @property string $name_en
 * @method static \Illuminate\Database\Eloquent\Builder|City whereNameEn($value)
 */
class City extends Model
{
	use HasFactory, SoftDeletes;
	
	const DEFAULT_CITY_NAME = 'Москва';
	const DEFAULT_CITY_NAME_EN = 'Moscow';
	
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
	const DC_ALIAS = 'dc';
	
	const RU_ALIASES = [
		self::MSK_ALIAS,
		self::SPB_ALIAS,
		self::VRN_ALIAS,
		self::KZN_ALIAS,
		self::KRD_ALIAS,
		self::NNV_ALIAS,
		self::SAM_ALIAS,
		self::EKB_ALIAS,
		self::NSK_ALIAS,
		self::KHV_ALIAS,
	];
	const EN_ALIASES = [
		self::DC_ALIAS,
	];

	const RU_VERSION = 'ru';
	const EN_VERSION = 'en';
	const VERSIONS = [
		self::RU_VERSION,
		self::EN_VERSION,
	];
	
	const IN_CITIES = [
		self::MSK_ALIAS => 'Москве',
		self::SPB_ALIAS => 'Санкт-Петербурге',
		self::VRN_ALIAS => 'Воронеже',
		self::KZN_ALIAS => 'Казани',
		self::KRD_ALIAS => 'Краснодаре',
		self::NNV_ALIAS => 'Нижнем Новгороде',
		self::SAM_ALIAS => 'Самаре',
		self::EKB_ALIAS => 'Екатеринбурге',
		self::NSK_ALIAS => 'Новосибириске',
		self::KHV_ALIAS => 'Хабаровске',
		self::DC_ALIAS => 'Washington DC',
	];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'name_en',
		'alias',
		'version',
		'timezone',
		'email',
		'phone',
		'whatsapp',
		'sort',
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

	public function phoneFormatted()
	{
		$phoneCleared = preg_replace( '/[^0-9]/', '', $this->phone);
		return '+' . mb_substr($phoneCleared, 0, 1) . ' (' . mb_substr($phoneCleared, 1, 3) . ') ' . mb_substr($phoneCleared, 4, 3) . '-' . mb_substr($phoneCleared, 7, 2) . '-' . mb_substr($phoneCleared, 9, 2);
	}

	/**
	 * Распределение оплат между локациями города
	 *
	 * @param Product|null $product
	 * @return Location|Location[]|\Illuminate\Database\Eloquent\Collection|Model|mixed|string|null
	 */
	public function getLocationForBill($product = null)
	{
		// по VIP-тарифам Леха и Окань вешаем все оплаты на Columbus
		if (in_array($product->alias, [Product::OKAN_ALIAS, Product::LEKHA_ALIAS])) {
			return HelpFunctions::getEntityByAlias(Location::class, Location::BUS_LOCATION);
		}
		
		$locations = $this->locations()
			->where('is_active', true)
			->whereNotNull('pay_account_number')
			->orderby('id')
			->get();
		if ($locations->isEmpty()) return null;
		
		if ($locations->count() > 1) {
			$locationids = $locations->pluck('id')->all();
			$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
			
			// проверяем локации последних двух Счетов (для Афимолла должно быть два Счета подряд)
			$lastTwoBillsLocationIds = Bill::whereIn('location_id', $locationids)
				->where('payment_method_id', $onlinePaymentMethod->id)
				->where('user_id', 0)
				/*->whereRelation('contractor', 'contractors.email', '!=', env('DEV_EMAIL'))*/
				->latest()->take(2)->pluck('location_id')->all();
			
			// если это город Москва
			if ($this->alias == City::MSK_ALIAS) {
				$afiLocation = HelpFunctions::getEntityByAlias(Location::class, Location::AFI_LOCATION);
				// если последний Счет на Афимолл, а предпоследний не на Афимолл, то выставляем Счет снова на Афимолл
				if ($afiLocation
					&& $lastTwoBillsLocationIds[0] == $afiLocation->id
					&& $lastTwoBillsLocationIds[0] != $lastTwoBillsLocationIds[1]
				) {
					return $afiLocation;
				}
			}
			
			// сдвигаем указатель в массиве локаций на следующую (или первую) локацию, на которую выставляем Счет
			$lastBillLocationIdKey = array_search($lastTwoBillsLocationIds[0], $locationids);
			$needLocationIdKey = isset($locationids[$lastBillLocationIdKey + 1]) ? $locationids[$lastBillLocationIdKey + 1] : $locationids[0];
			$location = Location::find($needLocationIdKey);
			
			return $location;
		}
		
		return $locations->first();
	}
}
