<?php

namespace App\Repositories;

use App\Models\Promocode;
use App\Models\User;

class PromocodeRepository {
	
	private $model;
	
	public function __construct(Promocode $promocode) {
		$this->model = $promocode;
	}
	
	/**
	 * @param User $user
	 * @param bool $onlyActive
	 * @param bool $onlyNoPersonal
	 * @return \Illuminate\Support\Collection
	 */
	public function getList(User $user, $onlyActive = true, $onlyNoPersonal = true)
	{
		$promocodes = $this->model->orderBy('number');
		if ($onlyActive) {
			$date = date('Y-m-d H:i:s');
			$promocodes = $promocodes->where('is_active', true)
				->where(function ($query) use ($date) {
					$query->where('active_from_at', '<=', $date)
						->orWhereNull('active_from_at');
				})
				->where(function ($query) use ($date) {
					$query->where('active_to_at', '>=', $date)
						->orWhereNull('active_to_at');
				});
		}
		if ($onlyNoPersonal) {
			$promocodes = $promocodes->where('contractor_id', 0);
		}
		if (!$user->isSuperAdmin() && $user->city) {
			$promocodes = $promocodes->whereRelation('cities', 'cities.id', '=', $user->city_id);
		}
		$promocodes = $promocodes->get();
		
		return $promocodes;
	}
}