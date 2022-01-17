<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PaymentMethod;
use App\Models\Deal;
use App\Models\Status;
use App\Services\HelpFunctions;
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
		
		$VIEW = view('admin.bill.modal.edit', [
			'bill' => $bill,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
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
		
		
		$amount = $deal->amount - $deal->billAmount();
		
		$statuses = Status::where('type', Status::STATUS_TYPE_BILL)
			->orderBy('sort')
			->get();

		$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.bill.modal.add', [
			'deal' => $deal,
			'amount' => ($amount > 0) ? $amount : 0,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
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
			'amount' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'deal_id' => 'Сделка',
				'payment_method_id' => 'Способ оплаты',
				'amount' => 'Сумма',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$deal = Deal::find($this->request->deal_id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		try {
			\DB::beginTransaction();

			$bill = new Bill();
			$bill->payment_method_id = $this->request->payment_method_id;
			$billStatus = HelpFunctions::getEntityByAlias('\App\Models\Status', Bill::NOT_PAYED_STATUS);
			$bill->status_id = $billStatus ? $billStatus->id : 0;
			$bill->amount = $this->request->amount;
			$bill->user_id = $this->request->user()->id;
			$bill->save();
			
			$deal->bills()->attach($bill->id);

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
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
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
		
		$bill->payment_method_id = $this->request->payment_method_id;
		$bill->status_id = $this->request->status_id;
		$bill->amount = $this->request->amount;

		if (!$bill->save()) {
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
		
		$email = '';
		foreach ($bill->deals ?? [] as $deal) {
			if ($deal->contractor) {
				$email = $deal->contractor->email;
				break;
			}
		}
		
		if (!$email) return response()->json(['status' => 'error', 'reason' => 'E-mail не найден']);
		
		$link = 'https://dream-aero.ru/pay/' . $bill->uuid;
		
		Mail::send('admin.emails.paylink', ['link' => $link], function ($message) use ($email) {
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
}
