<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $payment_method_id способ платежа
 * @property int $status_id статус
 * @property int $amount сумма платежа
 * @property \datetime|null $performed_at дата проведения платежа шлюзом или ОФД
 * @property int $deal_id сделка, к которой привязан платеж
 * @property array|null $data_json дополнительная информация: ОФД - номер смены, состав позиций, номер ФД, №пп, оператор. Шлюз -
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Bill|null $bill
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Query\Builder|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePerformedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Payment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Payment withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $number номер платежа
 * @property int $bill_id счет, по которому совершен платеж
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNumber($value)
 */
class Payment extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'bill_id' => 'Счет',
		'status_id' => 'Статус',
		'payment_method_id' => 'Способ оплаты',
		'amount' => 'Сумма',
		'data_json' => 'Дополнительная информация',
		'performed_at' => 'Оплачено',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	const NOT_SUCCEED_STATUS = 'not_succeed';
	const SUCCEED_STATUS = 'succeed';
	const STATUSES = [
		self::NOT_SUCCEED_STATUS,
		self::SUCCEED_STATUS,
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
		'bill_id',
		'status_id',
		'payment_method_id',
		'amount',
		'data_json',
		'performed_at',
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
		'performed_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
	];
	
	public static function boot() {
		parent::boot();
		
		Payment::created(function (Payment $payment) {
			$payment->number = $payment->generateNumber();
			if ($payment->save()) {
				$payment->bill->setStatus();
			}
		});
		
		Payment::saved(function (Payment $payment) {
			if ($payment->save()) {
				$payment->bill->setStatus();
			}
		});
		
		Payment::deleted(function (Payment $payment) {
			if ($payment->save()) {
				$payment->bill->setStatus();
			}
		});
	}
	
	public function paymentMethod()
	{
		return $this->hasOne('App\Models\PaymentMethod', 'id', 'payment_method_id');
	}
	
	public function bill()
	{
		return $this->belongsTo('App\Models\Bill', 'bill_id', 'id');
	}
	
	public function status()
	{
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}
	
	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$locationCount = $this->city ? $this->city->locations->count() : 0;
		$cityAlias = $this->city ? $this->city->alias : '';
		$locationAlias = $this->location ? $this->location->alias : '';
		$alias = ($locationCount > 1) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);
		
		return 'P' . date('y') . $alias . sprintf('%05d', $this->id);
	}
}
