<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
	use HasFactory, SoftDeletes;

	const BLACK_FRIDAY_TYPE = 'black-friday';
	const BLACK_FRIDAY_START = '2022-11-24 22:00:00';
	const BLACK_FRIDAY_STOP = '2022-11-25 23:00:00';
	const TYPES = [
		self::BLACK_FRIDAY_TYPE => 'Чёрная пятница',
	];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'email',
		'phone',
		'product_id',
		'city_id',
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
	];
	
	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}
	
	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}
}
