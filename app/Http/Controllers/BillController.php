<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\City;
use App\Models\Currency;
use App\Models\DealPosition;
use App\Models\PaymentMethod;
use App\Models\Deal;
use App\Models\Status;
use App\Services\HelpFunctions;
use App\Services\PayAnyWayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mail;
use Validator;
use Throwable;

class BillController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$bill = Bill::find($id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		
		$statuses = Status::where('type', Status::STATUS_TYPE_BILL)
			->orderBy('sort')
			->get();
		
		$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();

		$currencies = Currency::get();
		
		$deal = $bill->deal;
		$positions = $deal->positions;

		$VIEW = view('admin.bill.modal.edit', [
			'bill' => $bill,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
			'currencies' => $currencies,
			'positions' => $positions,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $dealId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$deal = Deal::find($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		
		$amount = $deal->amount() - $deal->billPayedAmount();
		
		$statuses = Status::where('type', Status::STATUS_TYPE_BILL)
			->orderBy('sort')
			->get();

		$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();

		$currencies = Currency::get();
		
		$positions = $deal->positions;

		$VIEW = view('admin.bill.modal.add', [
			'deal' => $deal,
			'amount' => ($amount > 0) ? $amount : 0,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
			'currencies' => $currencies,
			'positions' => $positions,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'deal_id' => 'required|numeric|min:0|not_in:0',
			'payment_method_id' => 'required|numeric|min:0|not_in:0',
			'status_id' => 'required|numeric|min:0|not_in:0',
			'amount' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'deal_id' => 'Сделка',
				'payment_method_id' => 'Способ оплаты',
				'status_id' => 'Статус',
				'amount' => 'Сумма',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$status = Status::find($this->request->status_id);
		if (!$status) {
			return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		}

		$deal = Deal::find($this->request->deal_id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		if (!$deal->contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$positionId = $this->request->position_id ?? 0;
		if ($positionId) {
			$position = DealPosition::find($positionId);
			if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция сделки  не найдена']);
		}
		
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		if ($paymentMethodId) {
			$paymentMethod = PaymentMethod::find($paymentMethodId);
		}

		try {
			\DB::beginTransaction();
			
			$location = $deal->city ? $deal->city->getLocationForBill() : null;
			if ($paymentMethod && $paymentMethod->alias == PaymentMethod::ONLINE_ALIAS && !$location) {
				\DB::rollback();
				
				Log::debug('500 - Bill Create: Не найден номер счета платежной системы');
				
				return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
			}
			
			$bill = new Bill();
			$bill->contractor_id = $deal->contractor->id ?? 0;
			$bill->deal_id = $deal->id ?? 0;
			$bill->deal_position_id = $position->id ?? 0;
			$bill->location_id = $location->id ?? 0;
			$bill->payment_method_id = $paymentMethodId;
			$bill->status_id = $this->request->status_id ?? 0;
			$bill->amount = $this->request->amount ?? 0;
			$bill->currency_id = $this->request->currency_id ?? 0;
			$bill->user_id = $this->request->user()->id;
			$bill->save();
			
			$deal->bills()->save($bill);

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Bill Create: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$bill = Bill::find($id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		
		$rules = [
			'payment_method_id' => 'required|numeric|min:0|not_in:0',
			'status_id' => 'required|numeric|min:0|not_in:0',
			'amount' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'payment_method_id' => 'Способ оплаты',
				'status_id' => 'Статус',
				'amount' => 'Сумма',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$status = Status::find($this->request->status_id);
		if (!$status) {
			return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		}
		
		$positionId = $this->request->position_id ?? 0;
		if ($positionId) {
			$position = DealPosition::find($positionId);
			if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция сделки  не найдена']);
		}
		
		$bill->deal_position_id = $position->id ?? 0;
		$bill->payment_method_id = $this->request->payment_method_id ?? 0;
		$bill->status_id = $this->request->status_id ?? 0;
		$bill->amount = $this->request->amount;
		$bill->currency_id = $this->request->currency_id ?? 0;
		if ($status->alias == Bill::PAYED_STATUS && !$bill->payed_at) {
			$bill->payed_at = Carbon::now()->format('Y-m-d H:i:s');
		}
		if (!$bill->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$bill = Bill::find($id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);

		if (!$bill->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	public function sendPayLink() {
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'bill_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'bill_id' => 'Счет',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$bill = Bill::find($this->request->bill_id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		
		if ($bill->amount <= 0) return response()->json(['status' => 'error', 'reason' => 'Сумма счета указана некорректно']);
		
		$deal = $bill->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$contractor = $bill->contractor;
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$city = $contractor->city;
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Город контрагента не найден']);
		
		$email = $deal->email ?: $contractor->email;
		if (!$email) return response()->json(['status' => 'error', 'reason' => 'E-mail не найден']);
		
		dispatch(new \App\Jobs\sendPayLinkEmail($bill));
		
		return response()->json(['status' => 'success', 'message' => 'Задание на отправку ссылки на оплату успешно создано']);
	}
}
