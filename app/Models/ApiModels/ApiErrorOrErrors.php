<?php

namespace App\ApiModels;

/**
 * @OA\Definition()
 */
class ApiErrorOrErrors {
	/**
	 * @OA\Property(
	 *     type="boolean",
	 *     default=false
	 * ),
	 */
	public $success;

	/**
	 * @OA\Property()
	 * @var string
	 */
	public $reason;

	/**
	 * @OA\Property(
	 *     type="array",
	 *     @OA\Items(
	 *         type="object",
	 *         @OA\Property(property="field", type="string"),
	 *         @OA\Property(property="reason", type="string")
	 *     )
	 * ),
	 */
	public $reasons;

	public function __construct($errors) {
		throw new \Exception('This class is only for documentation of integrapi! You should not use it anyway!');
	}//  __construct()

}
