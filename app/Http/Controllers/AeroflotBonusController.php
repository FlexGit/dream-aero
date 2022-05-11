<?php

namespace App\Http\Controllers;

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
		
		$minLimit = floor($amount / 100 * 20);

		$html = '
			<p>Сколько миль "Аэрофлот Бонус" Вы готовы списать?</p>
			<p id="ab-error" style="color: red;"></p>
			<div style="display: flex;">
				<input style="width: 48%;border-bottom: 2px solid #828285;margin-top: 10px;" name="bonus_amount" data-min="' . $minLimit . '" data-max="' . $cardInfoResult['max_limit'] . '" id="bonus_amount" type="text" value="" placeholder="Введите сумму в рублях" required="">
				<input style="width:48%;border-bottom: 2px solid #828285;margin-top:10px" readonly id="miles_amount" type="text" value="" required="">
			</div>
			<p>1 рубль = 4 мили</p>
			<i>Вы можете списать не менее 20% и не более 50% от стоимости тарифа</i>
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
	 * Получение состояния по ранее зарегистрированному заказу
	 *
	 * @return \Illuminate\Http\JsonResponse|null
	 */
	/*public function getOrderInfo()
	{
		$positions = DealPosition::where('transaction_type', AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER)
			->whereNotNull('aeroflot_transaction_order_id')
			->where('aeroflot_state', '!=', AeroflotBonusService::PAYED_STATE)
			->where('aeroflot_status', 0)
			->get();
		foreach ($positions as $position) {
			AeroflotBonusService::getOrderInfo($position);
		}
		
		return null;
	}*/
	
	/**
	 * Учет покупки по транзакции (вызывается из интерфейса)
	 *
	 */
	/*public function authPoints()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$positionId = $this->request->position_id ?? 0;
		if (!$positionId) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . trans('main.error.повторите-позже')]);
		}
		
		$position = DealPosition::whereNull('aeroflot_status')
			->find($positionId);
		if (!$position) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . trans('main.error.повторите-позже')]);
		}
		
		// учет покупки только для полностью оплаченных сделок
		if ($position->deal->balance() < 0) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . trans('main.error.некорректные-параметры')]);
		}
		
		$transaction = AeroflotBonusTransaction::where('transaction_type', AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS)
			->whereNull('status')
			->where($position->uuid)
			->oldest()
			->first();
		
		$result = AeroflotBonusService::authPoints($transaction->card_number ?? '', $position->uuid, $position->product->name, $position->amount);
		if (!$result) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.повторите-позже')]);
		}
		
		$position->aeroflot_status = $result['status']['code'];
		$position->save();
	}*/

	/**
	 * Учет покупки по списку транзакций (вызывается из CronTab)
	 *
	 */
	/*public function authPointsMass()
	{
		$positionId = $this->request->position_id ?? 0;
		if ($positionId) {
			$position = DealPosition::find($positionId);
		}
		
		$transactions = AeroflotBonusTransaction::where('transaction_type', AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS)
			->whereNull('status');
		if ($position) {
			$transactions = $transactions->where($position->uuid);
		}
		$transactions = $transactions->oldest()
			->get();
		foreach ($transactions as $transaction) {
			$position = DealPosition::where('uuid', $transaction->uuid)
				->first();
			if (!$position) continue;
			
			// учет покупки только для полностью оплаченных сделок
			if ($position->deal->balance() < 0) continue;
			
			$result = AeroflotBonusService::authPoints($transaction->card_number, $transaction->uuid, $position->product->name, $transaction->amount);
			if (!$result) {
				return response()->json(['status' => 'error', 'reason' => trans('main.error.повторите-позже')]);
			}
			
			$position->aeroflot_status = $result['status']['code'];
			$position->save();
		}
	}*/
	
	/**
	 * Повторная попытка создать заказ на списание милей
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
		
		$position = HelpFunctions::getEntityByUuid(DealPosition::class, $uuid);
		if (!$position
			|| $position->aeroflot_transaction_type != AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER
			|| !$position->aeroflot_card_number
			|| $position->aeroflot_bonus_amount <= 0
			|| $position->aeroflot_status != 0
			|| $position->amount <= 0) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		$registerOrderResult = AeroflotBonusService::registerOrder($position);
		if ($registerOrderResult['status']['code'] != 0) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . $registerOrderResult['status']['description']]);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Заявка успешно отправлена! Перенаправляем на страницу "Аэрофлот Бонус"...', 'payment_url' => $registerOrderResult['paymentUrl']]);
	}
	
	/**
	 * Обновление состояния заказа на списание милей
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
		
		$position = HelpFunctions::getEntityByUuid(DealPosition::class, $uuid);
		if (!$position
			|| $position->aeroflot_transaction_type != AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER
			|| !$position->aeroflot_transaction_order_id) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.некорректные-параметры')]);
		}
		
		if ($position->aeroflot_state == AeroflotBonusService::PAYED_STATE) {
			return response()->json(['status' => 'success', 'message' => 'Статус уже был обновлен ранее! Обновляем страницу...']);
		}

		if ($position->aeroflot_status != 0) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка при обновлении статуса. Попробуйте повторить попытку!']);
		}

		$orderInfoResult = AeroflotBonusService::getOrderInfo($position);
		$position = $position->fresh();
		if ($orderInfoResult['status']['code'] != 0) {
			return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . $orderInfoResult['status']['description']]);
		}
		
		if ($position->aeroflot_state == AeroflotBonusService::CANCEL_STATE) {
			return response()->json(['status' => 'error', 'reason' => 'Скидка "Аэрофлот Бонус" отклонена. Нажмите кнопку "Повторить попытку" для новой регистрации скидки в "Аэрофлот Бонус"!']);
		}
		
		if (!$position->aeroflot_state) {
			return response()->json(['status' => 'error', 'reason' => 'Скидка "Аэрофлот Бонус" пока не подтверждена. Попробуйте обновить статус позже!']);
		}
		
		if ($position->aeroflot_state == AeroflotBonusService::PAYED_STATE) {
			return response()->json(['status' => 'success', 'message' => 'Скидка успешно подтверждена! Обновляем страницу...']);
		}
		
		return response()->json(['status' => 'error', 'reason' => 'Скидка "Аэрофлот Бонус" не подтверждена. Неизвестная ошибка.']);
	}
}