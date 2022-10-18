<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Currency;
use App\Models\DealPosition;
use App\Models\PaymentMethod;
use App\Models\Deal;
use App\Models\ProductType;
use App\Models\Status;
use App\Services\AeroflotBonusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
	 * @param $dealId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$deal = Deal::find($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$amount = $deal->amount() - $deal->billPayedAmount();
		$statuses = Status::where('type', Status::STATUS_TYPE_BILL)
			->where('alias', '!=', Bill::PAYED_PROCESSING_STATUS)
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
			'user' => $user,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
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
		
		$user = \Auth::user();
		
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
		$billPositionIds = $bill->positions()->pluck('deal_positions.id')->toArray();

		$VIEW = view('admin.bill.modal.edit', [
			'bill' => $bill,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
			'currencies' => $currencies,
			'positions' => $positions,
			'billPositionIds' => $billPositionIds,
			'user' => $user,
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
		
		$user = \Auth::user();
		
		$rules = [
			'deal_id' => 'required|numeric|min:0|not_in:0',
			'payment_method_id' => 'required|numeric|min:0|not_in:0',
			'status_id' => 'required|numeric|min:0|not_in:0',
			'amount' => 'required|numeric|min:0',
			'position_id' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'deal_id' => 'Сделка',
				'payment_method_id' => 'Способ оплаты',
				'status_id' => 'Статус',
				'amount' => 'Сумма',
				'position_id' => 'Позиция',
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
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$amount = $this->request->amount ?? 0;
		/*if ($amount && !$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа для редактирования поля "Сумма"']);
		}*/

		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$positionsIds = $this->request->position_id ?? [];
		
		try {
			\DB::beginTransaction();
			
			$bill = new Bill();
			$bill->contractor_id = $deal->contractor->id ?? 0;
			$bill->deal_id = $deal->id ?? 0;
			$bill->location_id = $user->isAdminOBOrHigher() ? $deal->bill_location_id : ($this->request->user()->location_id ?? 0);
			$bill->payment_method_id = $paymentMethodId;
			$bill->status_id = $this->request->status_id ?? 0;
			if ($status->alias == Bill::PAYED_STATUS) {
				$bill->payed_at = Carbon::now()->format('Y-m-d H:i:s');
			}
			$bill->amount = $amount;
			$bill->currency_id = $this->request->currency_id ?? 0;
			$bill->user_id = $this->request->user()->id;
			$bill->save();
			
			$deal->bills()->save($bill);
			
			foreach ($positionsIds ?? [] as $positionsId) {
				$position = DealPosition::find($positionsId);
				if (!$position) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
				}
				
				$product = $position->product;
				if (!$product) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
				}
				
				$productType = $product->productType;
				if (!$productType) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
				}
				
				$city = $position->city;
				/*if (!$city) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
				}*/
				
				if ($city) {
					$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
					if (!$cityProduct || !$cityProduct->pivot) {
						\DB::rollback();
						return response()->json(['status' => 'error', 'reason' => 'Цена на продукт не указана']);
					}
					
					if ($productType->alias == ProductType::SERVICES_ALIAS && $deal->balance() >= 0) {
						$city->products()->updateExistingPivot($product->id, [
							'availability' => --$cityProduct->pivot->availability,
						]);
					}
				}
			}
			if ($positionsIds) {
				$bill->positions()->sync($positionsIds);
			}
			
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
		
		$user = \Auth::user();
		
		$billStatus = $bill->status;
		if ($billStatus && $billStatus->alias != Bill::NOT_PAYED_STATUS && !$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Счет в текущем статусе недоступен для редактирования']);
		}
		
		/*if ($bill->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER) {
			return response()->json(['status' => 'error', 'reason' => 'Счет недоступен для редактирования. Заявка на списание миль "Аэрофлот Бонус"']);
		}*/
		
		$deal = $bill->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS]) && !$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$rules = [
			'payment_method_id' => 'required|numeric|min:0|not_in:0',
			'status_id' => 'required|numeric|min:0|not_in:0',
			'amount' => 'required|numeric|min:0',
			'position_id' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'payment_method_id' => 'Способ оплаты',
				'status_id' => 'Статус',
				'amount' => 'Сумма',
				'position_id' => 'Позиция',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$status = Status::find($this->request->status_id);
		if (!$status) {
			return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		}
		
		if (in_array($bill->status->alias, [Bill::PAYED_STATUS, Bill::PAYED_PROCESSING_STATUS]) && $bill->paymentMethod && in_array($bill->paymentMethod->alias, [PaymentMethod::ONLINE_ALIAS]) && !$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Оплаченный Счет со способом оплаты "Онлайн" недоступен для редактирования']);
		}
		
		$paymentMethod = Status::find($this->request->payment_method_id);
		if (!$paymentMethod) {
			return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		}

		$amount = $this->request->amount ?? 0;
		/*if ($amount && !$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа для редактирования поля "Сумма"']);
		}*/
		
		$billStatus = $bill->status;
		$positionsIds = $this->request->position_id ?? [];
		
		try {
			\DB::beginTransaction();

			$bill->payment_method_id = $paymentMethod->id;
			$bill->status_id = $status->id;
			$bill->amount = $amount;
			if ($bill->status->alias == Bill::NOT_PAYED_STATUS
				&& $bill->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS
				&& !$bill->aeroflot_status
				&& $bill->aeroflot_state != AeroflotBonusService::PAYED_STATE
			) {
				$bill->aeroflot_bonus_amount = floor($amount / AeroflotBonusService::ACCRUAL_MILES_RATE);
			}
			$bill->currency_id = $this->request->currency_id ?? 0;
			if ($status->alias == Bill::PAYED_STATUS && !$bill->payed_at) {
				$bill->payed_at = Carbon::now()->format('Y-m-d H:i:s');
	
				// при выставлении даты оплаты генерим и дату окончания срока действия сертификата,
				// если это счет на позицию покупки сертификата
				$positions = $bill->positions;
				foreach ($positions as $position) {
					$certificate = $position ? $position->certificate : null;
					$certificateProduct = $certificate ? $certificate->product : null;
					$certificatePeriod = ($certificateProduct && $certificateProduct->validity && $position->is_certificate_purchase) ? $certificateProduct->validity : '';
					if ($certificate) {
						$certificate->expire_at = $certificatePeriod ? Carbon::now()->addMonths($certificatePeriod)->format('Y-m-d H:i:s') : null;
						$certificate->save();
					}
				}
			}
			$bill->save();
			
			foreach ($positionsIds ?? [] as $positionsId) {
				$position = DealPosition::find($positionsId);
				if (!$position) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
				}
				
				$product = $position->product;
				if (!$product) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
				}
				
				$productType = $product->productType;
				if (!$productType) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
				}
				
				$city = $position->city;
				/*if (!$city) {
					\DB::rollback();
					return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
				}*/
				
				if ($city) {
					$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
					if (!$cityProduct || !$cityProduct->pivot) {
						\DB::rollback();
						return response()->json(['status' => 'error', 'reason' => 'Цена на продукт не указана']);
					}
					
					if ($productType->alias == ProductType::SERVICES_ALIAS) {
						//\Log::debug($status->alias . ' - ' . $billStatus->alias . ' - ' . $deal->balance());
						if ($status->alias == Bill::PAYED_STATUS && (($billStatus && $billStatus->alias != Bill::PAYED_STATUS) || !$billStatus) && $deal->balance() >= 0) {
							$city->products()->updateExistingPivot($product->id, [
								'availability' => --$cityProduct->pivot->availability,
							]);
						}
						else if ($status->alias != Bill::PAYED_STATUS && $billStatus->alias == Bill::PAYED_STATUS) {
							$city->products()->updateExistingPivot($product->id, [
								'availability' => ++$cityProduct->pivot->availability,
							]);
						}
					}
				}
			}
			if ($positionsIds) {
				$bill->positions()->sync($positionsIds);
			}

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Bill Update: ' . $e->getMessage());
			
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
		
		$user = \Auth::user();
		
		if ($user->isAdmin() && $user->location_id && $bill->location_id && $user->location_id != $bill->location_id) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		/*if ($bill->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER) {
			return response()->json(['status' => 'error', 'reason' => 'Счет недоступен для удаления. Заявка на списание миль "Аэрофлот Бонус"']);
		}*/
		
		$deal = $bill->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($bill->status->alias, [Bill::PAYED_STATUS, Bill::PAYED_PROCESSING_STATUS]) && in_array($bill->paymentMethod->alias, [PaymentMethod::ONLINE_ALIAS])) {
			return response()->json(['status' => 'error', 'reason' => 'Оплаченный Счет со способом оплаты "Онлайн" недоступен для удаления']);
		}

		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$positions = $bill->positions;
		
		try {
			\DB::beginTransaction();
			
			foreach ($positions as $position) {
				$product = $position ? $position->product : null;
				$productType = $product ? $product->productType : null;
				$city = $product ? $position->city : null;
				$cityProduct = ($product && $city) ? $product->cities()->where('cities_products.is_active', true)->find($city->id) : null;
				
				if ($cityProduct && $cityProduct->pivot && $productType->alias == ProductType::SERVICES_ALIAS) {
					$city->products()->updateExistingPivot($product->id, [
						'availability' =>  ++$cityProduct->pivot->availability,
					]);
				}
				
				if (!$bill->positions()->detach($position->id)) {
					return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
				}
			}
			$bill->delete();

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Bill Delete: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteAeroflot($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$bill = Bill::find($id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		if (is_null($bill->aeroflot_transaction_type)) {
			return response()->json(['status' => 'error', 'reason' => 'Заявка не найдена']);
		}
		
		if (in_array($bill->status->alias, [Bill::CANCELED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Счет недоступен для редактирования']);
		}
		
		if ($bill->aeroflot_state == AeroflotBonusService::PAYED_STATE) {
			return response()->json(['status' => 'error', 'reason' => 'Заявка недоступна для удаления']);
		}
		
		$deal = $bill->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($bill->status->alias, [Bill::PAYED_STATUS, Bill::PAYED_PROCESSING_STATUS]) && in_array($bill->paymentMethod->alias, [PaymentMethod::ONLINE_ALIAS])) {
			return response()->json(['status' => 'error', 'reason' => 'Оплаченный Счет со способом оплаты "Онлайн" недоступен для редактирования']);
		}
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$bill->aeroflot_transaction_type = null;
		$bill->aeroflot_transaction_order_id = null;
		$bill->aeroflot_transaction_created_at = null;
		$bill->aeroflot_card_number = null;
		$bill->aeroflot_bonus_amount = 0;
		$bill->aeroflot_status = null;
		$bill->aeroflot_state = null;
		if (!$bill->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Заявка успешно отменена']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
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
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$contractor = $bill->contractor;
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$city = $contractor->city;
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Город контрагента не найден']);
		
		$email = $deal->email ?: $contractor->email;
		if (!$email) return response()->json(['status' => 'error', 'reason' => 'E-mail не найден']);
		
		//dispatch(new \App\Jobs\SendPayLinkEmail($bill));
		$job = new \App\Jobs\SendPayLinkEmail($bill);
		$job->handle();
		
		return response()->json(['status' => 'success', 'message' => 'Задание на отправку Ссылки на оплату принято']);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function accrualAeroflotMilesModal($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$bill = Bill::find($id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		
		$VIEW = view('admin.bill.modal.accrual-miles', [
			'bill' => $bill,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function accrualAeroflotMiles()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$rules = [
			'id' => 'required|numeric|min:0|not_in:0',
			'card_number' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'id' => 'Счет',
				'card_number' => 'Номер карты',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$id = $this->request->id ?? 0;
		$cardNumber = $this->request->card_number ?? '';
		
		$bill = Bill::find($id);
		if (!$bill) return response()->json(['status' => 'error', 'reason' => 'Счет не найден']);
		
		if ($bill->status->alias != Bill::PAYED_STATUS) {
			return response()->json(['status' => 'error', 'reason' => 'Счет не оплачен']);
		}
		
		if ($bill->amount <= 0) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная сумма Счета']);
		}
		
		$position = $bill->positions()->first();
		if (!$position) {
			return response()->json(['status' => 'error', 'reason' => 'К Счету не привязана позиция']);
		}

		if (!$position->is_certificate_purchase && Carbon::parse($bill->payed_at)->addDays(AeroflotBonusService::BOOKING_ACCRUAL_AFTER_DAYS)->lte(Carbon::now())) {
			return response()->json(['status' => 'error', 'reason' => 'Срок начисления миль по Счету истек']);
		}
		
		if ($position->is_certificate_purchase && Carbon::parse($bill->payed_at)->addDays(AeroflotBonusService::CERTIFICATE_PURCHASE_ACCRUAL_AFTER_DAYS)->lte(Carbon::now())) {
			return response()->json(['status' => 'error', 'reason' => 'Срок начисления миль по Счету истек']);
		}
		
		$product = $position->product;
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'К позиции не привязан продукт']);
		}
		
		$bill->aeroflot_transaction_type = AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS;
		$bill->aeroflot_card_number = $cardNumber;
		$bill->aeroflot_bonus_amount = floor($bill->amount / AeroflotBonusService::ACCRUAL_MILES_RATE);
		if (!$bill->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Заявка на начисление ' . $bill->aeroflot_bonus_amount . ' миль успешно создана']);
	}
}
