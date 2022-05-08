<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\DealPosition;
use Illuminate\Http\Request;
use App\Services\HelpFunctions;

class TestController extends Controller {
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	public function getModel($uuid)
	{
		$model = HelpFunctions::getEntityByUuid(Certificate::class, $uuid);
		
		dump($model->position);
	}
}