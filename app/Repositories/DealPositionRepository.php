<?php

namespace App\Repositories;

use App\Models\DealPosition;

class DealPositionRepository {
	
	private $model;
	
	public function __construct(DealPosition $position) {
		$this->model = $position;
	}
	
	/**
	 * @param $id
	 * @return DealPosition|DealPosition[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
	 */
	public function getById($id)
	{
		$position = $this->model->find($id);
		
		return $position;
	}
}