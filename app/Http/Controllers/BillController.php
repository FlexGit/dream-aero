<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\City;
use App\Models\Currency;
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

		$VIEW = view('admin.bill.modal.edit', [
			'bill' => $bill,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
			'currencies' => $currencies,
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
		
		$VIEW = view('admin.bill.modal.add', [
			'deal' => $deal,
			'amount' => ($amount > 0) ? $amount : 0,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
			'currencies' => $currencies,
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

		try {
			\DB::beginTransaction();

			$bill = new Bill();
			$bill->contractor_id = $deal->contractor->id ?? 0;
			$bill->payment_method_id = $this->request->payment_method_id;
			$bill->status_id = $this->request->status_id ?? 0;
			$bill->amount = $this->request->amount;
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
		
		$email = ($bill->deal && $bill->deal->contractor) ? $bill->deal->contractor->email : '';

		if (!$email) return response()->json(['status' => 'error', 'reason' => 'E-mail не найден']);
		
		$link = 'https://dream-aero.ru/pay/' . $bill->uuid;
		
		Mail::send('admin.emails.send_paylink', ['link' => $link], function ($message) use ($email) {
			$message->to($email)->subject('Ссылка на оплату');
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return response()->json(['status' => 'error', 'reason' => implode(' ', $failures)]);
		}

		$linkSentAt = Carbon::now()->format('Y-m-d H:i:s');
		$bill->link_sent_at = $linkSentAt;
		if (!$bill->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'link_sent_at' => $linkSentAt]);
	}

	/**
	 * @param $id
	 * @param $cityId
	 * @return \Illuminate\Http\JsonResponse|null
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function sendPayRequest($id, $cityId) {
		$city = City::where('is_active', true)
			->find($cityId);
		if(!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}

		$payAccountNumber = $city->pay_account_number ?? '';
		if (!$payAccountNumber) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректный номер счета платежной системы']);
		}

		$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
		if (!$billStatus) {
			return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		}

		$bill = Bill::where('status_id', $billStatus->id)
			->find($id);
		if(!$bill) {
			return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		}

		if (!$bill->deals) {
			return response()->json(['status' => 'error', 'reason' => 'Счет не привязан ни к одной сделке']);
		}

		$result = PayAnyWayService::sendPayRequest($payAccountNumber, $bill);

		return $result;
	}

	/**
	 * @return string
	 */
	public function payCallback() {
		$result = PayAnyWayService::checkPayCallback($this->request);

		if (!$result) {
			return 'FAIL';
		}

		$billId = $this->request->MNT_TRANSACTION_ID ?? 0;
		if ($billId) {
			$bill = Bill::find($billId);
			if ($bill && $bill->amount == $this->request->MNT_AMOUNT) {

			}
		}

		return 'SUCCESS';
	}

	public function paySuccess() {
		return 'success';
	}

	public function payFail() {
		return 'fail';
	}

	public function payReturn() {
		return 'cancel';
	}
}
