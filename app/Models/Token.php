<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

/**
 * App\Models\Token
 *
 * @property int $id
 * @property string $token токен
 * @property int $contractor_id контрагент
 * @property \datetime|null $expire_at Действует до
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Contractor $contractor
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Query\Builder|Token onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Token withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Token withoutTrashed()
 * @mixin \Eloquent
 */
class Token extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;

	/*const ATTRIBUTES = [
		'token' => 'Токен',
		'contractor_id' => 'Контрагент',
		'expire_at' => 'Действует до',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];*/

	protected $fillable = [
		'token',
		'contractor_id',
		'expire_at',
	];

	protected $dates = [
		'expire_at',
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'expire_at' => 'datetime:Y-m-d H:i:s',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
	];
	
	public static function boot()
	{
		parent::boot();

		Token::saved(function (Token $token) {
			// принудительно сбрасываем загруженные relations,
			// чтобы при необходимости подгрузились новые
			$token->setRelations([]);
			$token->deleteOldTokens();
			return true;
		});

		Token::created(function (Token $token) {
			$contractor = $token->contractor;
			if ($contractor) {
				$contractor->last_auth_at = new Carbon('now');
				$contractor->save();
			}

			// начисляем 500 баллов за первый вход (если соответствующая акция активна)
			if ($contractor->id == 70665) {
				\Log::debug($contractor->id . ' - ' . $contractor->tokens->count());
			}
			if ($contractor->tokens->count() == 1) {
				$promo = Promo::where('alias', 'registration_500_scores')
					->where('is_active', true)
					->first();
				if ($contractor->id == 70665) {
					\Log::debug($promo);
				}
				if ($promo && $promo->discount && $promo->discount->currency && $promo->discount->currency->alias == Currency::SCORE_ALIAS) {
					$score = new Score();
					$score->score = $promo->discount->value;
					$score->contractor_id = $contractor->id;
					$score->type = Score::SCORING_TYPE;
					$score->save();
					if ($contractor->id == 70665) {
						\Log::debug($score);
					}
				}
			}
			return true;
		});
	}
	
	public function contractor()
	{
		return $this->belongsTo(Contractor::class, 'contractor_id', 'id');
	}
	
	/**
	 * @param $contractor
	 */
	public function setToken($contractor)
	{
		$this->token = md5($contractor->email . ':' . $contractor->id . ':' . time() . rand(0, 99999));
	}

	/**
	 * @throws \Exception
	 */
	public function deleteOldTokens()
	{
		$lastValidCreated = self::where('contractor_id', $this->contractor_id)
			->orderBy('created_at', 'DESC')
			->limit(30)
			->get(['created_at'])
			->last();
		if ($lastValidCreated) {
			self::where('contractor_id', $this->contractor_id)
				->where('id', '!=', $this->id)
				->where('created_at', '<', $lastValidCreated->created_at->format('Y-m-d H:i:s'))
				->delete();
		}
	}

	public function format()
	{
		return [
			'token' => $this->token,
			'expire_at' => $this->expire_at,
		];
	}
}
