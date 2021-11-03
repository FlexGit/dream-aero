<?php

namespace App\ApiModels;

/**
 * @SWG\Definition()
 */
class ApiErrors {
	/**
	 * @SWG\Property()
	 * @var string
	 */
	public $success;

	/**
	 * @SWG\Property(
	 *     type="array",
	 *     @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(property="field", type="string"),
	 *         @SWG\Property(property="reason", type="string")
	 *     )
	 * ),
	 */
	public $reasons;

	public function __construct($errors) {
		$this->success = false;
		$this->reasons = $errors;
	}//  __construct()

}// class ApiError
