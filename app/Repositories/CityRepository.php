<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\User;

class CityRepository {
	
	private $model;
	
	public function __construct(City $city) {
		$this->model = $city;
	}
	
	/**
	 * @param $id
	 * @return City|City[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
	 */
	public function getById($id)
	{
		$city = $this->model->find($id);
		
		return $city;
	}
	
	/**
	 * @param User $user
	 * @param bool $onlyActive
	 * @return \Illuminate\Support\Collection
	 */
	public function getList(User $user, $onlyActive = true)
	{
		$cities = $this->model->orderBy('name')
			->where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		if (!$user->isSuperAdmin() && $user->city) {
			$cities = $cities->where('id', $user->city_id);
		}
		if ($onlyActive) {
			$cities = $cities->where('is_active', true);
		}
		
		return $cities;
	}
}