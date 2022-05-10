<?php

namespace App\Repositories;

use App\Models\Promo;
use App\Models\User;

class PromoRepository {
	
	private $model;
	
	public function __construct(Promo $promo) {
		$this->model = $promo;
	}
	
	/**
	 * @param User $user
	 * @param bool $onlyActive
	 * @return Promo[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getList(User $user, $onlyActive = true, $onlyWithDiscount = true)
	{
		$promos = $this->model->orderBy('name')
			->get();
		if (!$user->isSuperAdmin() && $user->city) {
			$promos = $promos->whereIn('city_id', [$user->city_id, 0]);
		}
		if ($onlyActive) {
			$promos = $promos->where('is_active', true);
		}
		if ($onlyWithDiscount) {
			$promos = $promos->where('discount_id', '!=', 0);
		}
		
		return $promos;
	}
}