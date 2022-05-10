<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PaymentRepository {
	
	private $model;
	
	public function __construct(PaymentMethod $paymentMethod) {
		$this->model = $paymentMethod;
	}
	
	public function getPaymentMethodList($onlyActive = true)
	{
		$paymentMethods = $this->model->orderBy('name')
			->get();
		if ($onlyActive) {
			$paymentMethods = $paymentMethods->where('is_active', true);
		}
		
		return $paymentMethods;
	}
}