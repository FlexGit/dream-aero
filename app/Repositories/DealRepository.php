<?php

namespace App\Repositories;

use App\Models\Deal;

class DealRepository {
	
	private $model;
	
	public function __construct(Deal $deal) {
		$this->model = $deal;
	}
	
	/**
	 * @param $id
	 * @return Deal|Deal[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
	 */
	public function getById($id)
	{
		$deal = $this->model->find($id);
		
		return $deal;
	}
}