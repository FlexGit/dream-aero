<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Status;

class StatusController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		return view('admin/status/index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$statuses = Status::get();
		$statusTypes = \App\Models\Status::STATUS_TYPES;

		$VIEW = view('admin.status.list', [
			'statuses' => $statuses,
			'statusTypes' => $statusTypes,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
}
