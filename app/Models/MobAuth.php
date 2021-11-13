<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Contractor;

/**
 * App\MobAuth
 *
 * @property int $id
 * @property string $token токен
 * @property int $contractor_id контрагент
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Contractor $contractor
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereId($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth query()
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth whereContractorId($value)
 */
class MobAuth extends Model {

	protected $fillable = [
		'id',
		'token',
		'contractor_id',
	];

	protected $dates = [
		'created_at',
		'updated_at',
	];

	public static function boot() {
		parent::boot();

		MobAuth::saved(function (MobAuth $mobAuth) {
			// принудительно сбрасываем загруженные relations, чтобы при необходимости подгрузились новые
			$mobAuth->setRelations([]);
			$mobAuth->deleteOldTokens();
			return true;
		});

		MobAuth::created(function (MobAuth $mobAuth) {
			$contractor = $mobAuth->contractor;
			if ($contractor) {
				$contractor->last_auth_at = new Carbon('now');
				$contractor->save();
			}
			return true;
		});
	}
	
	public function contractor() {
		return $this->belongsTo('App\Models\Contractor', 'contractor_id', 'id');
	}
	
	/**
	 * @param $contractor
	 */
	public function setToken($contractor) {
		$this->token = md5($contractor->email . ':' . $contractor->id . ':' . time() . rand(0, 99999));
	}

	/**
	 * @throws \Exception
	 */
	public function deleteOldTokens() {
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

}
