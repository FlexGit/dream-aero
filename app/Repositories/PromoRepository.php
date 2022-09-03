<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Content;
use App\Models\Promo;
use App\Models\User;
use App\Services\HelpFunctions;

class PromoRepository {
	
	private $model;
	
	public function __construct(Promo $promo) {
		$this->model = $promo;
	}
	
	/**
	 * @param User $user
	 * @param bool $onlyActive
	 * @param bool $onlyWithDiscount
	 * @param array $exceptAliases
	 * @return \Illuminate\Support\Collection
	 */
	public function getList(User $user, $onlyActive = true, $onlyWithDiscount = true, $exceptAliases = [])
	{
		$promos = $this->model->orderByDesc('active_from_at')
			->orderByDesc('created_at');
		if (!$user->isSuperAdmin() && $user->city) {
			$promos = $promos->whereIn('city_id', [$user->city_id, 0]);
		}
		if ($onlyActive) {
			$date = date('Y-m-d');
			$promos = $promos->where('is_active', true)
				->where(function ($query) use ($date) {
					$query->where('active_from_at', '<=', $date)
						->orWhereNull('active_from_at');
				})
				->where(function ($query) use ($date) {
					$query->where('active_to_at', '>=', $date)
						->orWhereNull('active_to_at');
				});
		}
		if ($onlyWithDiscount) {
			$promos = $promos->where('discount_id', '!=', 0);
		}
		if ($exceptAliases) {
			$promos = $promos->whereNotIn('alias', $exceptAliases);
		}
		$promos = $promos->get();
		
		return $promos;
	}
	
	/**
	 * @param City $city
	 * @return Content|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
	 */
	public function getActivePromobox(City $city)
	{
		$promoboxParentContent = HelpFunctions::getEntityByAlias(Content::class, Content::PROMOBOX_TYPE);
		if (!$promoboxParentContent) return null;
		
		$date = date('Y-m-d');
		
		return Content::where('parent_id', $promoboxParentContent->id)
			->where('is_active', true)
			->where('published_at', '<=', $date)
			->where(function ($query) use ($date) {
				$query->where('published_end_at', '>=', $date)
					->orWhereNull('published_end_at');
			})
			->whereIn('city_id', [$city->id, 0])
			->orderByDesc('published_at')
			->first();
	}
}