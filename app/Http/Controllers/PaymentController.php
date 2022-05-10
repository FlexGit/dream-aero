<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Content;
use App\Models\PaymentMethod;
use App\Models\Status;
use App\Services\AeroflotBonusService;
use App\Services\HelpFunctions;
use App\Services\PayAnyWayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Bill;

class PaymentController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * @param $uuid
	 * @param null $type
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function payment($uuid, $type = null)
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, PaymentMethod::ONLINE_ALIAS);
		$notPayedStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
		
		$bill = Bill::where('uuid', $uuid)
			->where('location_id', '!=', 0)
			->first();
		if (!$bill) {
			abort(404);
		}
		if ($bill->paymentMethod->alias != $onlinePaymentMethod->alias) {
			return view('payment', [
				'page' => $page ?? new Content,
				'city' => $city,
				'html' => '',
				'error' => trans('main.pay.счет-способ-оплаты'),
				'payType' => '',
			]);
		}
		if ($bill->status->alias != $notPayedStatus->alias || $bill->payed_at != null) {
			return view('payment', [
				'page' => $page ?? new Content,
				'city' => $city,
				'html' => '',
				'error' => trans('main.pay.счет-оплачен'),
				'payType' => '',
			]);
		}
		
		$position = $bill->position;
		// автоматическая проверка состояния заказа на списание милей, если такой есть
		if ($position
			&& $position->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER
			&& $position->aeroflot_transaction_order_id
			&& $position->aeroflot_status == 0
			&& $position->aeroflot_state == AeroflotBonusService::PAYED_STATE) {
			$orderInfoResult = AeroflotBonusService::getOrderInfo($position);
		}
		
		$paymentFormHtml = PayAnyWayService::generatePaymentForm($bill);
		
		return view('payment', [
			'page' => $page ?? new Content,
			'city' => $city,
			'bill' => $bill,
			'html' => $paymentFormHtml ?? '',
			'error' => '',
			'payType' => $type ?? '',
		]);
	}
	
	/**
	 * Ответ на уведомление об оплате от сервиса Монета
	 *
	 * @return string
	 */
	public function paymentCallback()
	{
		\Log::debug('paymentCallback');
		\Log::debug($this->request);
		
		$uuid = $this->request->MNT_TRANSACTION_ID ?? '';
		if (!$uuid) {
			return 'FAIL';
		}
		
		$result = PayAnyWayService::paymentCallback($this->request);
		if (!$result) {
			return 'FAIL';
		}
		
		$bill = HelpFunctions::getEntityByUuid(Bill::class, $uuid);
		if (!$bill) {
			return 'FAIL';
		}
		
		if ($bill->status && $bill->status->alias != Bill::PAYED_STATUS) {
			$payedStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::PAYED_STATUS);
			$bill->status_id = $payedStatus->id;
			$bill->payed_at = Carbon::now()->format('Y-m-d H:i:s');
			if(!$bill->save()) {
				return 'FAIL';
			}
			
			$position = $bill->position;
			if ($position) {
				$certificate = $position->certificate;
				if ($position->is_certificate_purchase && $certificate) {
					$certificate = $certificate->generateFile();
					
					$job = new \App\Jobs\sendCertificateEmail($certificate);
					dispatch($job);
					//$job->handle();
				}
				
				// если было выбрано начисление бонусов Аэрофлот
				if ($position->aeroflot_transaction_type == AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS) {
					AeroflotBonusService::authPoints($position);
				}
			}
		}
		
		return 'SUCCESS';
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function paymentSuccess()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'rules');
		
		$data = [
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'error' => '',
			'message' => '',
		];
		
		$uuid = $this->request->MNT_TRANSACTION_ID ?? '';
		if (!$uuid) {
			$data['error'] = trans('main.payment.счет-не-найден');
			return view('payment-success', $data);
		}
		
		$bill = HelpFunctions::getEntityByUuid(Bill::class, $uuid);
		if (!$bill) {
			$data['error'] = trans('main.payment.счет-не-найден');
			return view('payment-success', $data);
		}
		
		if ($bill->status && $bill->status->alias != Bill::NOT_PAYED_STATUS) {
			$data['error'] = trans('main.payment.счет-уже-был-оплачен', ['number' => $bill->number]);
			return view('payment-success', $data);
		}
		
		$payedStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::PAYED_PROCESSING_STATUS);
		$bill->status_id = $payedStatus->id;
		$bill->save();
		
		$position = $bill->position;
		if ($position) {
			if ($position->is_certificate_purchase) {
				$data['message'] = trans('main.payment.оплата-успешно-принята-сертификат-будет-отправлен', ['number' => $bill->number]);
			} else {
				$data['message'] = trans('main.payment.оплата-успешно-принята-приглашение-на-полет-будет-отправлено', ['number' => $bill->number]);
			}
		} else {
			$data['message'] = trans('main.payment.оплата-успешно-принята', ['number' => $bill->number]);
		}
		
		return view('payment-success', $data);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function paymentFail()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'rules');
		
		$uuid = $this->request->MNT_TRANSACTION_ID ?? '';
		if ($uuid) {
			$bill = HelpFunctions::getEntityByUuid(Bill::class, $uuid);
		}
		
		return view('payment-fail', [
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'error' => trans('main.payment.оплата-счета-отклонена', ['number' => $bill->number ?? '']),
		]);
	}
	
}