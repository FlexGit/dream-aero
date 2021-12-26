<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Bill
 *
 * @property int $id
 * @property string|null $number номер счета
 * @property int $status_id статус
 * @property int $amount сумма счета
 * @property int $deal_id сделка, по которой выставлен счет
 * @property int $deal_position_id позиция сделки, по которой выставлен счет
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property int $user_id пользователь
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Deal|null $deal
 * @property-read \App\Models\DealPosition|null $dealPosition
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newQuery()
 * @method static \Illuminate\Database\Query\Builder|Bill onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Bill withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Bill withoutTrashed()
 * @mixin \Eloquent
 */
class Bill extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер счета',
		'status_id' => 'Статус счета',
		'amount' => 'Сумма',
		'deal_id' => 'Сделка',
		'deal_position_id' => 'Позиция сделки',
		'is_active' => 'Активность',
		'data_json' => 'Дополнительная информация',
		'user_id' => 'Пользователь',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	const NOT_PAYED_STATUS = 'not_payed';
	const PAYED_STATUS = 'payed';
	const STATUSES = [
		self::NOT_PAYED_STATUS,
		self::PAYED_STATUS,
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
		'status_id',
		'amount',
		'deal_id',
		'deal_position_id',
		'is_active',
		'data_json',
		'user_id',
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
	
	public static function boot() {
		parent::boot();
		
		Bill::created(function (Bill $bill) {
			$bill->number = $bill->generateNumber();
			$bill->save();
		});
		
		Bill::deleting(function(Bill $bill) {
			$bill->payments()->delete();
		});
	}

	public function status()
	{
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}
	
	public function deal()
	{
		return $this->belongsTo('App\Models\Deal', 'deal_id', 'id');
	}
	
	public function dealPosition()
	{
		return $this->belongsTo('App\Models\DealPosition', 'deal_position_id', 'id');
	}
	
	public function payments()
	{
		return $this->hasMany('App\Models\Payment', 'bill_id', 'id');
	}
	
	/**
	 * @return string
	 */
	public function generateNumber()
	{
		$locationCount = ($this->dealPosition && $this->dealPosition->city) ? $this->dealPosition->city->locations->count() : 0;
		$cityAlias = $this->dealPosition->city ? $this->dealPosition->city->alias : '';
		$locationAlias = $this->dealPosition->location ? $this->dealPosition->location->alias : '';
		$alias = ($locationCount > 1) ? mb_strtolower($locationAlias) : mb_strtolower($cityAlias);
		
		return 'B' . date('y') . $alias  . sprintf('%05d', $this->id);
	}
	
	/**
	 * Изменение статуса Счета на основании текущих платежей
	 */
	public function setStatus()
	{
		$paymentSum = 0;
		foreach ($this->payments ?? [] as $payment) {
			if ($payment->status->alias != Payment::SUCCEED_STATUS) {
				continue;
			}
			$paymentSum += $payment->amount;
		}
		$statusesData = HelpFunctions::getStatusesByType();
		if ($paymentSum >= $this->amount) {
			$this->update(['status_id' => $statusesData['bill'][Bill::PAYED_STATUS]['id']]);
		} else {
			$this->update(['status_id' => $statusesData['bill'][Bill::NOT_PAYED_STATUS]['id']]);
		}
	}
}
