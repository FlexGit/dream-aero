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
		$promocodes = $this->model->orderBy('number')
			->get();
		if (!$user->isSuperAdmin() && $user->city) {
			$promocodes = $promocodes->whereIn('city_id', [$user->city_id, 0]);
		}
		if ($onlyActive) {
			$promocodes = $promocodes->where('is_active', true);
		}
		if ($onlyNoPersonal) {
			$promocodes = $promocodes->where('contractor_id', 0);
		}
		
		return $promocodes;
	}
}