<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Bill
 *
 * @property int $id
 * @property string|null $number номер счета
 * @property int $payment_method_id способ оплаты
 * @property int $status_id статус
 * @property int $amount сумма счета
 * @property string|null $uuid
 * @property \datetime|null $payed_at дата проведения платежа
 * @property \datetime|null $link_sent_at
 * @property int $user_id пользователь
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Deal[] $deals
 * @property-read int|null $deals_count
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
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
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereLinkSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePayedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|Bill withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Bill withoutTrashed()
 * @mixin \Eloquent
 * @property int $contractor_id
 * @property-read \App\Models\Contractor|null $contractor
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereContractorId($value)
 * @property int $deal_id
 * @property int $currency_id
 * @property-read \App\Models\Currency|null $currency
 * @property-read \App\Models\Deal|null $deal
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDealId($value)
 * @property int $deal_position_id
 * @property int $location_id локация, по которой был выставлен счет
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\DealPosition|null $position
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereLocationId($value)
 * @property \Illuminate\Support\Carbon|null $success_payment_sent_at дата и время отправки уведомления об успешной оплате
 * @property string|null $aeroflot_transaction_type
 * @property string|null $aeroflot_transaction_order_id
 * @property string|null $aeroflot_card_number
 * @property int $aeroflot_bonus_amount
 * @property string|null $aeroflot_status
 * @property string|null $aeroflot_state
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AeroflotBonusLog[] $aeroflotBonusLog
 * @property-read int|null $aeroflot_bonus_log_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotBonusAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotTransactionOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereSuccessPaymentSentAt($value)
 * @property \Illuminate\Support\Carbon|null $aeroflot_transaction_created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAeroflotTransactionCreatedAt($value)
 */
class Bill extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер счета',
		'contractor_id' => 'Контрагент',
		'deal_id' => 'Сделка',
		'deal_position_id' => 'Позиция сделки',
		'payment_method_id' => 'Способ оплаты',
		'status_id' => 'Статус счета',
		'amount' => 'Сумма',
		'currency_id' => 'Валюта',
		'location_id' => 'Локация, по которой был выставлен счет',
		'uuid' => 'Uuid',
		'data_json' => 'Дополнительная информация',
		'user_id' => 'Пользователь',
		'payed_at' => 'Оплачено',
		'link_sent_at' => 'Ссылка на оплату отправлена',
		'success_payment_sent_at' => 'Уведомление об успешной оплате отправлено',
		'aeroflot_transaction_type' => 'Тип транзакции',
		'aeroflot_transaction_order_id' => 'ID транзакции/заказа',
		'aeroflot_transaction_created_at' => 'Дата и время создания транзакции',
		'aeroflot_card_number' => 'Номер карты',
		'aeroflot_bonus_amount' => 'Сумма бонуса',
		'aeroflot_status' => 'Статус операции',
		'aeroflot_state' => 'Состояние заказа',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	const NOT_PAYED_STATUS = 'bill_not_payed';
	const PAYED_PROCESSING_STATUS = 'bill_payed_processing';
	const PAYED_STATUS = 'bill_payed';
	const CANCELED_STATUS = 'bill_canceled';
	const STATUSES = [
		self::NOT_PAYED_STATUS,
		self::PAYED_PROCESSING_STATUS,
		self::PAYED_STATUS,
		self::CANCELED_STATUS,
	];
	
	const CASH_PAYMENT_METHOD = 'cash';
	const CARD_PAYMENT_METHOD = 'card';
	const BANK_PAYMENT_METHOD = 'bank';
	const ONLINE_PAYMENT_METHOD = 'online';
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	protected $dontKeepRevisionOf = ['uuid', 'data_json'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'contractor_id',
		'deal_id',
		'deal_position_id',
		'payment_method_id',
		'status_id',
		'amount',
		'currency_id',
		'location_id',
		'uuid',
		'payed_at',
		'link_sent_at',
		'success_payment_sent_at',
		'aeroflot_transaction_type',
		'aeroflot_transaction_order_id',
		'aeroflot_transaction_created_at',
		'aeroflot_card_number',
		'aeroflot_bonus_amount',
		'aeroflot_status',
		'aeroflot_state',
		'user_id',
		'data_json',
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'payed_at' => 'datetime:Y-m-d H:i:s',
		'link_sent_at' => 'datetime:Y-m-d H:i:s',
		'success_payment_sent_at' => 'datetime:Y-m-d H:i:s',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
		'aeroflot_transaction_created_at' => 'datetime:Y-m-d H:i:s',
	];
	
	public static function boot() {
		parent::boot();
		
		Bill::created(function (Bill $bill) {
			$bill->number = $bill->generateNumber();
			$bill->uuid = $bill->generateUuid();
			$bill->save();
			
			if ($bill->user_id) {
				$deal = $bill->deal;
				$createdStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
				if ($deal->status_id == $createdStatus->id) {
					$inWorkStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::IN_WORK_STATUS);
					if ($inWorkStatus) {
						$deal->status_id = $inWorkStatus->id;
						$deal->save();
					}
				}
			}
		});

		Bill::saved(function (Bill $bill) {
			$deal = $bill->deal;

			if ($bill->user_id && $bill->created_at != $bill->updated_at) {
				$createdStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
				if ($deal->status_id == $createdStatus->id) {
					$inWorkStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::IN_WORK_STATUS);
					if ($inWorkStatus) {
						$deal->status_id = $inWorkStatus->id;
						$deal->save();
					}
				}
			}

			if ($deal->status
				&& !in_array($deal->status->alias, [Deal::RETURNED_STATUS, Deal::CANCELED_STATUS, Deal::CONFIRMED_STATUS, Deal::PAUSED_STATUS])
				&& $deal->balance() >= 0
			) {
				$confirmedStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CONFIRMED_STATUS);
				if ($confirmedStatus) {
					$deal->status_id = $confirmedStatus->id;
					$deal->save();
				}
			}
			
			/*if ($deal->status
				&& !in_array($deal->status->alias, [Deal::RETURNED_STATUS, Deal::CANCELED_STATUS, Deal::IN_WORK_STATUS, Deal::PAUSED_STATUS])
				&& $deal->balance() < 0
			) {
				$inWorkStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::IN_WORK_STATUS);
				if ($inWorkStatus) {
					$deal->status_id = $inWorkStatus->id;
					$deal->save();
				}
			}*/
		});
	}

	public function contractor()
	{
		return $this->hasOne(Contractor::class, 'id', 'contractor_id');
	}

	public function deal()
	{
		return $this->belongsTo(Deal::class);
	}
	
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function status()
	{
		return $this->hasOne(Status::class, 'id', 'status_id');
	}
	
	public function paymentMethod()
	{
		return $this->hasOne(PaymentMethod::class, 'id', 'payment_method_id');
	}
	
	public function position()
	{
		return $this->hasOne(DealPosition::class, 'id', 'deal_position_id');
	}

	public function currency()
	{
		return $this->hasOne(Currency::class, 'id', 'currency_id');
	}
	
	public function location()
	{
		return $this->belongsTo(Location::class);
	}
	
	public function aeroflotBonusLog()
	{
		return $this->hasMany(AeroflotBonusLog::class, 'bill_id', 'id');
	}

	/**
	 * @return string
	 */
	public function generateNumber()
	{
		return 'B' . date('y') . sprintf('%05d', $this->id);
	}
	
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function generateUuid()
	{
		return (string)\Webpatser\Uuid\Uuid::generate();
	}
}
