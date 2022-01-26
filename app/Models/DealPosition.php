<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

class DealPosition extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'deal_id' => 'Сделка',
		'product_id' => 'Продукт',
		'certificate_id' => 'Сертификат',
		'duration' => 'Длительность',
		'amount' => 'Стоимость',
		'currency_id' => 'Валюта',
		'city_id' => 'Город',
		'location_id' => 'Локация',
		'flight_simulator_id' => 'Авиатренажер',
		'promo_id' => 'Акция',
		'promocode_id' => 'Промокод',
		'is_certificate_purchase' => 'Покупка сертификата',
		'flight_at' => 'Дата полета',
		'invite_sent_at' => 'Дата отправки приглашения',
		'certificate_sent_at' => 'Дата отправки сертификата',
		'source' => 'Источник',
		'user_id' => 'Пользователь',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
		'certificate_whom' => 'Для кого сертифкат',
		'comment' => 'Комментарий',
	];
	
	const ADMIN_SOURCE = 'admin';
	const WEB_SOURCE = 'web';
	const MOB_SOURCE = 'api';
	const SOURCES = [
		self::ADMIN_SOURCE => 'Админка',
		self::WEB_SOURCE => 'Web',
		self::MOB_SOURCE => 'Mob',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'deal_id',
		'product_id',
		'certificate_id',
		'duration',
		'amount',
		'currency_id',
		'city_id',
		'location_id',
		'flight_simulator_id',
		'promo_id',
		'promocode_id',
		'is_certificate_purchase',
		'flight_at',
		'invite_sent_at',
		'certificate_sent_at',
		'user_id',
		'source',
		'data_json',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'flight_at' => 'datetime:Y-m-d H:i',
		'invite_sent_at' => 'datetime:Y-m-d H:i',
		'certificate_sent_at' => 'datetime:Y-m-d H:i',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'is_certificate_purchase' => 'boolean',
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();

		DealPosition::created(function (DealPosition $dealPosition) {
			$dealPosition->number = $dealPosition->generateNumber();
			$dealPosition->save();
		});

		DealPosition::saved(function (DealPosition $dealPosition) {
			if (!$dealPosition->user_id && $dealPosition->source == Deal::ADMIN_SOURCE) {
				$dealPosition->user_id = \Auth::user()->id;
				$dealPosition->save();
			}
		});

		DealPosition::deleting(function(DealPosition $position) {
			$position->certificate()->delete();
		});
	}
	
	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id')
			->withPivot('price', 'is_hit');
	}

	public function currency()
	{
		return $this->hasOne(Currency::class, 'id', 'currency_id');
	}

	public function certificate()
	{
		return $this->hasOne(Certificate::class, 'id', 'certificate_id');
	}

	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}

	public function location()
	{
		return $this->hasOne(Location::class, 'id', 'location_id');
	}

	public function simulator()
	{
		return $this->hasOne(FlightSimulator::class, 'id', 'flight_simulator_id');
	}

	public function deal()
	{
		return $this->belongsTo(Deal::class);
	}
	
	public function event()
	{
		return $this->hasOne(Event::class, 'deal_position_id', 'id');
	}
	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
	
	public function promocode()
	{
		return $this->hasOne(Promocode::class, 'id', 'promocode_id');
	}
	
	public function promo()
	{
		return $this->hasOne(Promo::class, 'id', 'promo_id');
	}

	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$locationCount = ($this->city && $this->city->locations) ? $this->city->locations->count() : 1;

		$cityAlias = $this->city ? mb_strtolower($this->city->alias) : '';
		$locationAlias = $this->location ? $this->location->alias : '';

		$productTypeAlias = ($this->product && $this->product->productType) ? mb_strtoupper(substr($this->product->productType->alias, 0, 1)) : '';
		$productDuration = $this->product ? $this->product->duration : '';

		$alias = ($locationCount > 1 && $locationAlias) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);

		return 'P' . date('y') . $alias . $productTypeAlias . $productDuration . sprintf('%05d', $this->id);
	}
}
