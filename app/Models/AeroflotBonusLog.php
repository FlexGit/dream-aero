<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\AeroflotBonusLog
 *
 * @property int $id
 * @property int $deal_position_id
 * @property string|null $transaction_order_id
 * @property string|null $transaction_type
 * @property int $amount сумма
 * @property int $bonus_amount
 * @property string|null $card_number номер карты
 * @property string|null $status статус
 * @property string|null $state
 * @property string|null $request тело запроса
 * @property string|null $response тело ответа
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\DealPosition|null $position
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AeroflotBonusLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereBonusAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereTransactionOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AeroflotBonusLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AeroflotBonusLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AeroflotBonusLog withoutTrashed()
 * @mixin \Eloquent
 */
class AeroflotBonusLog extends Model
{
	use HasFactory, SoftDeletes;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'deal_position_id',
		'transaction_order_id',
		'transaction_type',
		'amount',
		'bonus_amount',
		'card_number',
		'status',
		'state',
		'request',
		'response',
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
	
	public function position()
	{
		return $this->hasOne(DealPosition::class, 'id', 'deal_position_id');
	}
}
