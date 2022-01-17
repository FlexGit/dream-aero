<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

class Status extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
    const STATUS_TYPE_CONTRACTOR = 'contractor';
	/*const STATUS_TYPE_ORDER = 'order';*/
	const STATUS_TYPE_DEAL = 'deal';
	const STATUS_TYPE_CERTIFICATE = 'certificate';
	const STATUS_TYPE_BILL = 'bill';
	/*const STATUS_TYPE_PAYMENT = 'payment';*/
	const STATUS_TYPES = [
		Status::STATUS_TYPE_CONTRACTOR => 'Контрагент',
		/*Status::STATUS_TYPE_ORDER => 'Заказ',*/
		Status::STATUS_TYPE_DEAL => 'Сделка',
		Status::STATUS_TYPE_CERTIFICATE => 'Сертификат',
		Status::STATUS_TYPE_BILL => 'Счет',
		/*Status::STATUS_TYPE_PAYMENT => 'Платеж',*/
	];
	
	const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'type' => 'Сущность',
		'sort' => 'Сортировка',
		'is_active' => 'Признак активности',
		'data_json' => 'Дополнительная информация',
		'flight_time' => 'Налет',
		'discount_id' => 'Скидка',
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
		'type',
		'flight_time',
		'discount_id',
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
		'data_json' => 'array',
		'is_active' => 'boolean',
	];
}
