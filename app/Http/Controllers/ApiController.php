<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\City;
use App\Models\Contractor;
use App\Models\LegalEntity;
use App\Models\MobAuth;
use App\Models\Code;
use App\Models\Promo;
use App\Models\TariffType;
use App\Models\Tariff;
use App\Models\Location;

use Illuminate\Http\Request;
use Validator;
use Mail;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;
use Throwable;
use Illuminate\Support\Facades\Log;

use App\Traits\ApiResponser;

class ApiController extends Controller
{
	use ApiResponser;
	
	private $request;
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function login() {
		$rules = [
			'email' => ['required', 'email'],
			'password' => ['required', 'string'],
		];
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'email' => 'E-mail',
				'password' => 'Пароль',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key][] = $error;
				}
			}
			return $this->responseError($errors, 400);
		}
		
		$contractor = Contractor::where('email', $this->request->email)
			->first();
		if (!$contractor) {
			return $this->responseError(['email' => 'Указанный E-mail не найден. Проверьте введенные данные.'], 400);
		}
		
		if ($contractor->password !== $this->request->password) {
			return $this->responseError(['password' => 'Пароль указан неверно'], 400);
		}
		
		$mobAuth = new MobAuth();
		$mobAuth->contractor_id = $contractor->id;
		$mobAuth->setToken($contractor);
		
		if ($mobAuth->save()) {
			return $this->responseSuccess(null, [$mobAuth->toArray()]);
		}
		
		return $this->responseError('В данный момент невозможно пройти авторизацию, попробуйте позже', 500);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function logout() {
		$contractorId = $this->request->contractor_id;
		$token = $this->request->token;
		
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		if (!$token) {
			return $this->responseError('Не передан токен', 400);
		}
		
		$mobAuth = MobAuth::where('token', $token)
			->where('contractor_id', $contractorId)
			->first();
		
		if (!$mobAuth) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$mobAuth->delete();
		
		return $this->responseSuccess('Токен успешно удален');
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendCode() {
		$rules = [
			'email' => ['required', 'email'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'email' => 'E-mail',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key][] = $error;
				}
			}
			return $this->responseError($errors, 400);
		}
		
		$email = $this->request->email;
		$type = $this->request->type ?? '';
		
		$contractor = Contractor::where('email', $email)
			->first();
		
		if ($type && in_array($type, ['password_restore']) && !$contractor) {
			return $this->responseError(['email' => 'Указанный E-mail не найден. Проверьте введенные данные.'], 400);
		}
		
		$lastCode = Code::where('email', $email)
			->where('is_reset', false)
			->orderBy('created_at', 'desc')
			->first();
		if($lastCode) {
			$secondsDiff = $lastCode->created_at->diffInSeconds(Carbon::now());
			if ($secondsDiff < Contractor::RESEND_CODE_INTERVAL) {
				return $this->responseError('Получить код можно через ' . (Contractor::RESEND_CODE_INTERVAL - $secondsDiff) . ' секунд');
			}
		}
		
		try {
			$codeValue = rand(1000, 9999);
			
			Mail::send('admin.emails.code', ['code' => $codeValue], function ($message) use ($email) {
				$message->to($email)->subject('Код подтверждения');
			});
			
			$failures = Mail::failures();
			if ($failures) {
				return $this->responseError(implode(' ', $failures), 500);
			}
		} catch (Throwable $e) {
			Log::debug('500 - ' . $e->getMessage());
			
			return $this->responseError('Ошибка, попробуйте позже', '500', $e->getMessage() . ' - ' . $this->request->url());
		}
		
		$code = new Code();
		$code->code = $codeValue;
		$code->email = $email;
		$code->contractor_id = $contractor->id ?? 0;
		$code->save();
		
		return $this->responseSuccess('Код подтверждения отправлен на ' . $email, [$code->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function verifyCode() {
		$rules = [
			'email' => ['required', 'email'],
			'code' => ['required', 'digits:4'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'email' => 'E-mail',
				'code' => 'Код',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key][] = $error;
				}
			}
			return $this->responseError($errors, 400);
		}
		
		$email = $this->request->email;
		$codeValue = $this->request->code;
		
		$code = Code::where('code', $codeValue)
			->where('email', $email)
			->where('is_reset', false)
			->where('created_at', '>', Carbon::now()->subSeconds(Contractor::CODE_TTL))
			->first();
		if (!$code) {
			return $this->responseError('Некорректный код. Проверьте данные и повторите попытку снова.', 400);
		}
		
		$code->is_reset = true;
		$code->reset_at = Carbon::now();
		$code->save();
		
		return $this->responseSuccess('Код подтвержден');
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function register() {
		$rules = [
			'contractor_id' => ['prohibited', 'numeric'],
			'password' => ['required', 'confirmed'/*, Password::defaults()*/],
			'password_confirmation' => ['required', 'same:password'],
			'email' => ['required_without:contractor_id', 'email'],
			'name' => ['required_without:contractor_id', 'min:3', 'max:50'],
			'birthdate' => ['required_without:contractor_id', 'date'],
			'city_id' => ['required_without:contractor_id', 'numeric', 'valid_city'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'password' => 'Пароль',
				'password_confirmation' => 'Повторный пароль',
				'email' => 'E-mail',
				'name' => 'Имя',
				'birthdate' => 'Дата рождения',
				'city_id' => 'Город'
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key][] = $error;
				}
			}
			return $this->responseError($errors, 400);
		}
		
		$contractorId = $this->request->contractor_id;
		
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return $this->responseError('Контрагент не найден', 400);
			}
		} else {
			$data = [
				'birthdate' => Carbon::parse($this->request->birthdate)->format('Y-m-d'),
			];
			$contractor = new Contractor();
			$contractor->name = $this->request->name;
			$contractor->email = $this->request->email;
			$contractor->city_id = $this->request->city_id;
			$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		}
		
		$contractor->password = $this->request->password;
		if (!$contractor->save()) {
			return $this->responseError('Попробуйте повторить позже', 500);
		}
		
		$mobAuth = new MobAuth();
		$mobAuth->contractor_id = $contractor->id;
		$mobAuth->setToken($contractor);
		$mobAuth->save();
		
		$data = [
			'contractor' => $contractor->toArray(),
			'auth' => $mobAuth ? $mobAuth->toArray() : [],
		];

		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function resetPassword() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$rules = [
			'password' => ['required', 'confirmed'/*, Password::defaults()*/],
			'password_confirmation' => ['same:password'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'password' => 'Пароль',
				'password_confirmation' => 'Повторный пароль',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key][] = $error;
				}
			}
			return $this->responseError($errors, 400);
		}
		
		$password = $this->request->password;
		
		$contractor = Contractor::find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor->password = $password;
		if ($contractor->save()) {
			return $this->responseSuccess('Пароль успешно изменен', [$contractor->toArray()]);
		}
		
		return $this->responseError('Попробуйте повторить позже', 500);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$contractor = Contractor::find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		return $this->responseSuccess(null, [$contractor->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function saveProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$rules = [
			'name' => ['required', 'min:3', 'max:50'],
			'email' => ['required', 'email'],
			'birthdate' => ['required', 'date'],
			'city_id' => ['required', 'numeric', 'valid_city'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'birthdate' => 'Дата рождения',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key][] = $error;
				}
			}
			return $this->responseError($errors, 400);
		}
		
		$contractor = Contractor::find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$data = [
			'birthdate' => Carbon::parse($this->request->birthdate)->format('Y-m-d'),
		];
		$contractor->name = $this->request->name;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone;
		$contractor->city_id = $this->request->city_id;
		$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		if ($contractor->save()) {
			return $this->responseSuccess('Профиль успешно сохранен', [$contractor->toArray()]);
		}
		
		return $this->responseError('Попробуйте повторить позже', 500);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$contractor = Contractor::find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		if ($contractor->delete()) {
			return $this->responseSuccess('Профиль успешно удален');
		}
		
		return $this->responseError('Попробуйте повторить позже', 500);
	}
	
	public function saveAvatar() {
	
	}
	
	public function deleteAvatar() {
	
	}
	
	public function getNotifications() {
	
	}
	
	public function getNotification() {
	
	}
	
	public function getFlights() {
	
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getTariffTypes() {
		$tariffTypes = TariffType::where('is_active', true)
			->get();
		
		return $this->responseSuccess(null, [$tariffTypes->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getTariffs() {
		$tariffTypeId = $this->request->tariff_type_id;
		if (!$tariffTypeId) {
			return $this->responseError('Не передан ID типа тарифа', 400);
		}
		
		$tariffs = Tariff::where('tariff_type_id', $tariffTypeId)
			->where('is_active', true)
			->with(['tarifType', 'employee', 'city'])
			->get();
		
		if ($tariffs->isEmpty()) {
			return $this->responseError('Тарифы не найдены', 400);
		}
		
		return $this->responseSuccess(null, [$tariffs->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getTariff() {
		$tariffId = $this->request->tariff_id;
		if (!$tariffId) {
			return $this->responseError('Не передан ID тарифа', 400);
		}
		
		$tariff = Tariff::where('is_active', true)
			->with(['tarifType', 'employee', 'city'])
			->find($tariffId);
		if (!$tariff) {
			return $this->responseError('Тариф не найден', 400);
		}
		
		return $this->responseSuccess(null, [$tariff->toArray()]);
	}
	
	public function verifyCertificate() {
	
	}
	
	public function verifyPromocode() {
	
	}
	
	public function getPrice() {
	
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCities() {
		$cities = City::where('is_active', true)
			->get();
		
		return $this->responseSuccess(null, [$cities->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getLocations() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$locations = Location::where('city_id', $cityId)
			->where('is_active', true)
			->with(['city', 'legalEntity', 'simulator'])
			->get();
		
		if ($locations->isEmpty()) {
			return $this->responseError('Локации не найдены', 400);
		}
		
		return $this->responseSuccess(null, [$locations->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getLegalEntities() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$legalEntityIds = Location::where('city_id', $cityId)
			->where('is_active', true)
			->pluck('legal_entity_id')
			->all();
		
		$legalEntities = LegalEntity::whereIn('id', $legalEntityIds)
			->where('is_active', true)
			->get();
		
		if ($legalEntities->isEmpty()) {
			return $this->responseError('Юридический лица не найдены', 400);
		}
		
		$legalEntitiesData = [];
		foreach ($legalEntities as $legalEntity) {
			$legalEntitiesData[] = [
				'id' => $legalEntity->id,
				'name' => $legalEntity->name,
				'public_offer_file_path' => $this->request->getSchemeAndHttpHost() . '/upload/' . $legalEntity->data_json['public_offer_file_path'],
				'created_at' => $legalEntity->created_at ? Carbon::parse($legalEntity->created_at)->format('Y-m-d H:i:s') : null,
				'updated_at' => $legalEntity->updated_at ? Carbon::parse($legalEntity->updated_at)->format('Y-m-d H:i:s') : null,
			];
		}
		
		return $this->responseSuccess(null, [$legalEntitiesData]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getPromos() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$promos = Promo::where('city_id', $cityId)
			->where('is_active', true)
			->with(['city'])
			->get();
		
		if ($promos->isEmpty()) {
			return $this->responseError('Акции не найдены', 400);
		}
		
		return $this->responseSuccess(null, [$promos->toArray()]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getPromo() {
		$promoId = $this->request->promo_id;
		if (!$promoId) {
			return $this->responseError('Не передан ID акции', 400);
		}
		
		$promo = Promo::where('is_active', true)
			->with(['city'])
			->find($promoId);
		if (!$promo) {
			return $this->responseError('Акция не найдена', 400);
		}
		
		return $this->responseSuccess(null, [$promo->toArray()]);
	}
}