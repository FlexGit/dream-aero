<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Deal
 *
 * @property int $id
 * @property string|null $number номер сделки
 * @property int $contractor_id контрагент, с которым заключена сделка
 * @property array|null $data_json дополнительная информация
 * @property int $user_id пользователь
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DealPosition[] $dealPositions
 * @property-read int|null $deal_positions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newQuery()
 * @method static \Illuminate\Database\Query\Builder|Deal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Deal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Deal withoutTrashed()
 * @mixin \Eloquent
 */
class Deal extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'number' => 'Номер',
		'status_id' => 'Статус',
		'contractor_id' => 'Контрагент',
		'order_id' => 'Заказ',
		'data_json' => 'Дополнительная информация',
		'user_id' => 'Пользователь',
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
		'number',
		'status_id',
		'contractor_id',
		'order_id',
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
	];
	
	public static function boot() {
		parent::boot();
		
		Deal::created(function (Deal $deal) {
			$deal->number = $deal->generateNumber();
			$deal->save();
		});

		Deal::deleting(function(Deal $deal) {
			$deal->bills()->delete();
			$deal->dealPositions()->delete();
		});
	}
	
	public function contractor()
	{
		return $this->belongsTo('App\Models\Contractor', 'contractor_id', 'id');
	}
	
	public function dealPositions()
	{
		return $this->hasMany('App\Models\DealPosition', 'id', 'deal_id');
	}
	
	public function bills()
	{
		return $this->hasMany('App\Models\Bill', 'deal_id', 'id');
	}
	
	public function user()
	{
		return $this->hasOne('App\Models\User', 'user_id', 'id');
	}
	
	/**
	 * @return string
	 */
	public function generateNumber()
	{
		return 'D' . date('y') . sprintf('%05d', $this->id);
	}
}
