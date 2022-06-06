<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\DealPosition;
use App\Services\AeroflotBonusService;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\City;
use App\Services\HelpFunctions;

class AeroflotBonusController extends Controller {
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	public function getCardInfo()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'card' => 'required',
			'product_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'card' => 'Номер карты "Аэрофлот Бонус"',
				'product_id' => 'Тариф',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key] = $error;
				}
			}
			return response()->json(['status' => 'error', 'errors' => $errors]);
		}
		
		$cardNumber = $this->request->card ?? '';
		$productId = $this->request->product_id ?? 0;
		$amount = $this->request->amount ?? 0;
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.повторите-позже')]);
		}
		
		$cardInfoResult = AeroflotBonusService::getCardInfo($cardNumber, $product, $amount);
		if (!$cardInfoResult) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.повторите-позже')]);
		}
		
		$minLimit = floor($amount / 100 * 10);

		$html = '
			<p>Сколько миль "Аэрофлот Бонус" Вы готовы списать?</p>
			<p id="ab-error" style="color: red;"></p>
			<div style="display: flex;">
				<input style="width: 50%;border-bottom: 2px solid #828285;margin-top: 10px;" name="bonus_amount" data-min="' . $minLimit . '" data-max="' . $cardInfoResult['max_limit'] . '" id="bonus_amount" type="text" value="" placeholder="Введите сумму в рублях" required="">
				<input style="width: 50%;border-bottom: 2px solid #828285;margin-top: 10px;" readonly id="miles_amount" type="text" value="" required="">
			</div>
			<p>1 рубль = 4 мили</p>
			<i>Вы можете списать не менее 10% и не более 50% от стоимости тарифа</i>
		';
		
		return response()->json(['status' => 'success', 'html' => $html]);
	}
	
	public function cardVerify()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cardNumber = $this->request->card ?? '';
		if (!$cardNumber) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.не-передан-номер-карты')]);
		}
		
		$html = '<ul class="aerbonus_btns">
					<li id="charge" class="plan-time">
						' . trans('main.modal-certificate.начислить-мили') . '
					</li>
					<li id="use" class="plan-time">
						' . trans('main.modal-certificate.использовать-мили') . '
					</li>
				</ul>
				<div id="bonus_info"></div>';
		
		if (strlen($cardNumber) <= 8 && strlen($cardNumber) >= 4) {
			$cardNumber = str_pad($cardNumber, 10, "0", STR_PAD_LEFT);
			$cardNumberArr = str_split($cardNumber, 1);
			$sum = 0;
			for ($i = 0; $i <= 8; $i++) {
				$sum += $cardNumberArr[$i] * ($i + 1);
			}
			if (substr($cardNumber, -1) == substr($sum, -1)) {
				return response()->json(['status' => 'success', 'message' => 'Номер введен верно', 'html' => $html]);
			}
		}
		
		if (strlen($cardNumber) <= 10 && strlen($cardNumber) >= 8) {
			$cardNumberStep1 = substr($cardNumber, 0,strlen($cardNumber) - 1);
			$cardNumberStep2 = floor(intval($cardNumberStep1) / 7) * 7;
			$cardNumberStep3 = $cardNumberStep1 - $cardNumberStep2;
			if (substr($cardNumberStep3, -1) == substr($cardNumber, -1)){
				return response()->json(['status' => 'success', 'message' => 'Номер введен верно', 'html' => $html]);
			}
		}
		
		return response()->json(['status' => 'error', 'reason' => trans('main.modal-certificate.номер-карты-введен-неверно')]);
	}
	
	/**
	 * Повторная попытка создать заказ на списание миль
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function useRetry()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$uuid = $this->request->uuid ?? null;
		if (!$uuid) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		$bill = HelpFunctions::getEntityByUuid(Bill::class, $uuid);
		if (!$bill
			|| $bill->aeroflot_transaction_type != AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER
			|| !$bill->aeroflot_card_number
			|| $bill->aeroflot_bonus_amount <= 0
			|| $bill->aeroflot_status != 0
			|| $bill->amount <= 0) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		$registerOrderResult = AeroflotBonusService::registerOrder($bill);
		if ($registerOrderResult['status']['code'] != 0) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . $registerOrderResult['status']['description']]);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Заявка успешно отправлена! Перенаправляем на страницу "Аэрофлот Бонус"...', 'payment_url' => $registerOrderResult['paymentUrl']]);
	}
	
	/**
	 * Обновление состояния заказа на списание миль
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function useRefresh()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$uuid = $this->request->uuid ?? null;
		if (!$uuid) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		$bill = HelpFunctions::getEntityByUuid(Bill::class, $uuid);
		if (!$bill
			|| $bill->aeroflot_transaction_type != AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER
			|| !$bill->aeroflot_transaction_order_id) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		if ($bill->aeroflot_state == AeroflotBonusService::PAYED_STATE) {
			return response()->json(['status' => 'success', 'message' => 'Статус уже был обновлен ранее! Обновляем страницу...']);
		}

		if ($bill->aeroflot_status != 0) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка при обновлении статуса. Попробуйте повторить попытку!']);
		}

		$orderInfoResult = AeroflotBonusService::getOrderInfo($bill);
		$bill = $bill->fresh();
		if ($orderInfoResult['status']['code'] != 0) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . $orderInfoResult['status']['description']]);
		}
		
		if ($bill->aeroflot_state == AeroflotBonusService::CANCEL_STATE) {
			return response()->json(['status' => 'error', 'reason' => 'Скидка "Аэрофлот Бонус" отклонена. Нажмите кнопку "Повторить попытку" для новой регистрации скидки в "Аэрофлот Бонус"!']);
		}
		
		if (!$bill->aeroflot_state) {
			return response()->json(['status' => 'error', 'reason' => 'Скидка "Аэрофлот Бонус" пока не подтверждена. Попробуйте обновить статус позже!']);
		}
		
		if ($bill->aeroflot_state == AeroflotBonusService::PAYED_STATE) {
			return response()->json(['status' => 'success', 'message' => 'Скидка успешно подтверждена! Обновляем страницу...']);
		}
		
		return response()->json(['status' => 'error', 'reason' => 'Скидка "Аэрофлот Бонус" не подтверждена. Неизвестная ошибка.']);
	}
	
	/**
	 * Создание транзакции на начисление/списание миль при оплате по ссылке
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function transaction()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$uuid = $this->request->uuid ?? '';
		$transactionType = $this->request->transaction_type ?? '';
		$cardNumber = $this->request->card_number ?? '';
		$bonusAmount = $this->request->bonus_amount ?? 0;
		
		if (!$uuid) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		$bill = HelpFunctions::getEntityByUuid(Bill::class, $uuid);
		if (!$bill || $bill->amount <= 0) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		if ($bill->status->alias != Bill::NOT_PAYED_STATUS) {
			return response()->json(['status' => 'error', 'reason' => trans('main.pay.счет-оплачен')]);
		}
		
		if ($transactionType && $cardNumber) {
			if ($bill->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER) {
				return response()->json(['status' => 'error', 'reason' => 'Начисление миль невозможно. Ранее уже было выбрано Списание']);
			}
			
			switch ($transactionType) {
				case AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER:
					if (!$bonusAmount) {
						return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
					}
					
					$bill->aeroflot_transaction_type = $transactionType;
					$bill->aeroflot_card_number = $cardNumber;
					$bill->aeroflot_bonus_amount = $bonusAmount;
					$bill->save();
					$bill = $bill->fresh();
					
					$registerOrderResult = AeroflotBonusService::registerOrder($bill);
					if ($registerOrderResult['status']['code'] != 0) {
						\Log::debug('500 - Payment: ' . $registerOrderResult['status']['description']);
						
						return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . $registerOrderResult['status']['description']]);
					}
					
					return response()->json(['status' => 'success', 'message' => 'Перенаправляем на страницу "Аэрофлот Бонус"...', 'payment_url' => $registerOrderResult['paymentUrl']]);
				break;
				case AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS:
					$bill->aeroflot_transaction_type = $transactionType;
					$bill->aeroflot_card_number = $cardNumber;
					$bill->aeroflot_bonus_amount = floor($bill->amount / AeroflotBonusService::ACCRUAL_MILES_RATE);
					$bill->save();
					
					return response()->json(['status' => 'success', 'message' => 'Перенаправляем на страницу оплаты...']);
				break;
			}
		}
		
		return response()->json(['status' => 'success', 'message' => 'Перенаправляем на страницу оплаты...']);
	}
}