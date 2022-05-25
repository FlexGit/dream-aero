<?php

namespace App\Repositories;

use App\Models\Status;

class StatusRepository {
	
	private $model;
	
	public function __construct(Status $status) {
		$this->model = $status;
	}
	
	/**
	 * @param $type
	 * @return Status[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getList($type)
	{
		$statuses = $this->model->where('type', $type)
			->orderBy('sort')
			->get();
		
		return $statuses;
	}
}