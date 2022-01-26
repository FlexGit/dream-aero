<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Discount
 *
 * @property int $id
 * @property int|null $value размер скидки
 * @property bool $is_fixed фиксированная скидка
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newQuery()
 * @method static \Illuminate\Database\Query\Builder|Discount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereIsFixed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|Discount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Discount withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $alias алиас
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereAlias($value)
 * @property int $currency_id
 * @property-read \App\Models\Currency|null $currency
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereCurrencyId($value)
 */
class Discount extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'value' => 'Размер скидки',
		'is_fixed' => 'Фиксированная скидка',
		'currency_id' => 'Валюта',
		'is_active' => 'Активность',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	const DISCOUNT_5_ALIAS = 'percent_5';
	const DISCOUNT_10_ALIAS = 'percent_10';
	const DISCOUNT_15_ALIAS = 'percent_15';
	const DISCOUNT_20_ALIAS = 'percent_20';
	const DISCOUNT_25_ALIAS = 'percent_25';
	const DISCOUNT_30_ALIAS = 'percent_30';
	const DISCOUNT_35_ALIAS = 'percent_35';
	const DISCOUNT_40_ALIAS = 'percent_40';
	const DISCOUNT_45_ALIAS = 'percent_45';
	const DISCOUNT_50_ALIAS = 'percent_50';
	const DISCOUNT_FIXED_500_ALIAS = 'fixed_500';
	const DISCOUNT_FIXED_1000_ALIAS = 'fixed_1000';
	const DISCOUNT_FIXED_1500_ALIAS = 'fixed_1500';
	const DISCOUNT_FIXED_2000_ALIAS = 'fixed_2000';
	const DISCOUNT_FIXED_2500_ALIAS = 'fixed_2500';
	const DISCOUNT_FIXED_3000_ALIAS = 'fixed_3000';
	const DISCOUNT_FIXED_5000_ALIAS = 'fixed_5000';

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'value',
		'is_fixed',
		'currency_id',
		'is_active',
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
		'is_fixed' => 'boolean',
	];

	public function currency()
	{
		return $this->hasOne(Currency::class, 'id', 'currency_id');
	}

	public function format() {
		return [
			'value' => $this->value,
			'is_fixed' => $this->is_fixed,
		];
	}
	
	public function valueFormatted() {
		return number_format($this->value, 0, '.', ' ') . ' ' . ($this->is_fixed ? ($this->currency ? $this->currency->name : '') : '%');
	}
}
