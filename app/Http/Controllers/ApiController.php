<?php

namespace App\Http\Controllers;

use App\Models\DealPosition;
use App\Models\Notification;
use App\Services\HelpFunctions;
use Illuminate\Http\Request;
use Validator;
use Mail;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\City;
use App\Models\Contractor;
use App\Models\LegalEntity;
use App\Models\Token;
use App\Models\Code;
use App\Models\Promo;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\Location;
use App\Models\Promocode;
use App\Models\Order;
use App\Models\Deal;
use App\Models\Score;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Certificate;
use App\Models\Status;

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
	 * Authentification
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam email string required No-example
	 * @queryParam password string required Password (md5). No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"token": {
	 * 			"token": "328dda59f036efc26720937545efe01e",
	 * 			"expire_at": null
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function login()
	{
		$rules = [
			'email' => ['required', 'email'],
			'password' => ['required', 'string'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
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
		
		$contractor = Contractor::where('is_active', true)
			->where('email', $this->request->email)
			->first();
		if (!$contractor) {
			return $this->responseError(['email' => 'Указанный E-mail не найден. Проверьте введенные данные.'], 400);
		}
		
		if ($contractor->password !== $this->request->password) {
			return $this->responseError(['password' => 'Пароль указан неверно'], 400);
		}
		
		$token = new Token();
		$token->contractor_id = $contractor->id;
		$token->setToken($contractor);

		if (!$token->save()) {
			return $this->responseError(null, 500);
		}
		
		$data = [
			'token' => $token->token,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Logout
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Токен успешно удален",
	 * 	"data": null
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function logout()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		if ($token->delete()) {
			return $this->responseSuccess('Токен успешно удален');
		}
		
		return $this->responseError(null, 500);
	}
	
	/**
	 * Verification code send
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam email string required No-example
	 * @queryParam type string Action type. For Registration - empty value, for Password Restore - "password_restore". No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Код подтверждения отправлен на john.smith@gmail.com",
	 * 	"data": null
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function sendCode()
	{
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
		
		$contractor = Contractor::where('is_active', true)
			->where('email', $email)
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
				return $this->responseError('Получить код можно через ' . (Contractor::RESEND_CODE_INTERVAL - $secondsDiff) . ' секунд', 400);
			}
		}
		
		try {
			$codeValue = rand(1000, 9999);
			
			$code = new Code();
			$code->code = $codeValue;
			$code->email = $email;
			$code->contractor_id = $contractor->id ?? 0;
			$code->save();

			Mail::send('admin.emails.code', ['code' => $codeValue], function ($message) use ($email) {
				$message->to($email)->subject('Код подтверждения');
			});
			
			$failures = Mail::failures();
			if ($failures) {
				return $this->responseError(implode(' ', $failures), 500);
			}
		} catch (Throwable $e) {
			Log::debug('500 - ' . $e->getMessage());
			
			return $this->responseError(null, '500', $e->getMessage() . ' - ' . $this->request->url());
		}
		
		return $this->responseSuccess('Код подтверждения отправлен на ' . $email);
	}
	
	/**
	 * Code verify
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam email string required No-example
	 * @queryParam code string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Код подтвержден",
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function verifyCode()
	{
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
		if (!$code->save()) {
			return $this->responseError(null, 500);
		}
		
		return $this->responseSuccess('Код подтвержден');
	}

	/**
	 * Registration
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int No-example
	 * @bodyParam password string required Password (md5). No-example
	 * @bodyParam password_confirmation string required Password confirmation (md5). No-example
	 * @bodyParam email string No-example
	 * @bodyParam name string No-example
	 * @bodyParam birthdate date No-example
	 * @bodyParam city_id int No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 		"token": {
	 * 			"token": "6136d60c36e6925bf98dea7e05d5f5c8",
	 * 			"expire_at": null
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function register()
	{
		$rules = [
			'contractor_id' => ['nullable', 'numeric'],
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
		
		$contractorId = $this->request->contractor_id;
		
		if ($contractorId) {
			$contractor = Contractor::where('is_active', true)
				->find($contractorId);
			if (!$contractor) {
				return $this->responseError('Контрагент не найден', 400);
			}
		} else {
			$contractor = Contractor::where('email', $this->request->email)
				->first();
			if ($contractor) {
				return $this->responseError('Контрагент с таким E-mail уже существует', 400);
			}
			$contractor = new Contractor();
			$contractor->name = $this->request->name;
			$contractor->email = $this->request->email;
			$contractor->city_id = $this->request->city_id;
			$contractor->birthdate = Carbon::parse($this->request->birthdate)->format('Y-m-d');
		}
		
		$contractor->password = $this->request->password;
		
		try {
			\DB::beginTransaction();
			
			$contractor->save();
			
			$token = new Token();
			$token->contractor_id = $contractor->id;
			$token->setToken($contractor);
			$token->save();

			$contractor->last_auth_at = date('Y-m-d H:i:s');
			$contractor->save();
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - ' . $e->getMessage());
			
			return $this->responseError(null, '500', $e->getMessage() . ' - ' . $this->request->url());
		}

		$data = [
			'contractor' => $contractor->format(),
			'token' => $token->format(),
		];

		return $this->responseSuccess('Регистрация успешно завершена', $data);
	}
	
	/**
	 * Password change
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @bodyParam password string required Password (md5). No-example
	 * @bodyParam password_confirmation string required Password confirmation (md5). No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Пароль успешно изменен",
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"password": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function resetPassword()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
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
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor->password = $password;
		if (!$contractor->save()) {
			return $this->responseError(null, 500);
		}
		
		$data = [
			'contractor' => $contractor->format(),
		];

		return $this->responseSuccess('Пароль успешно изменен', $data);
	}
	
	/**
	 * Profile
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getProfile()
	{
		$authToken = $this->request->token;
		if (!$authToken) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}

		$data = [
			'contractor' => $contractor->format(),
			'city' => $contractor->city ? $contractor->city->format() : null,
		];

		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Profile save
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @bodyParam email string required No-example
	 * @bodyParam name string required No-example
	 * @bodyParam lastname string No-example
	 * @bodyParam birthdate date required No-example
	 * @bodyParam phone string +71234567890 No-example
	 * @bodyParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Профиль успешно сохранен",
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function saveProfile()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$rules = [
			'name' => ['required', 'min:3', 'max:50'],
			'lastname' => ['sometimes', 'required', 'min:3', 'max:50'],
			'email' => ['required', 'email'],
			'phone' => ['sometimes', 'required', 'valid_phone'],
			'birthdate' => ['required', 'date'],
			'city_id' => ['required', 'numeric', 'valid_city'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'name' => 'Имя',
				'lastname' => 'Фамилия',
				'email' => 'E-mail',
				'phone' => 'Телефон',
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
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor->name = $this->request->name;
		$contractor->lastname = $this->request->lastname ?? null;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone ?? null;
		$contractor->city_id = $this->request->city_id;
		$contractor->birthdate = Carbon::parse($this->request->birthdate)->format('Y-m-d');
		
		if (!$contractor->save()) {
			return $this->responseError(null, 500);
		}
		
		$data = [
			'contractor' => $contractor->format(),
		];
		
		return $this->responseSuccess('Профиль успешно сохранен', $data);
	}
	
	/**
	 * Profile delete
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Профиль успешно удален",
	 * 	"data": null
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function deleteProfile()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		try {
			\DB::beginTransaction();
			
			$contractor->delete();
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - ' . $e->getMessage());
			
			return $this->responseError(null, '500', $e->getMessage() . ' - ' . $this->request->url());
		}
		
		return $this->responseSuccess('Профиль успешно удален');
	}
	
	/**
	 * Profile Reset
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Аккаунт контрагента успешно очищен",
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function resetProfile()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}

		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor->password = null;
		$contractor->lastname = null;
		$contractor->birthdate = null;
		$contractor->city_id = 0;
		$contractor->discount_id = 0;
		$contractor->is_active = 1;
		$contractor->data_json = null;
		$contractor->last_auth_at = null;
		if (!$contractor->save()) {
			return $this->responseError(null, 500);
		}
		
		$data = [
			'contractor' => $contractor->format(),
		];
		
		return $this->responseSuccess('Аккаунт контрагента успешно очищен', $data);
	}

	/**
	 * Avatar save
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @bodyParam file_base64 string required data:image/jpeg;base64,/9j/7gAhQWR...
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Файл успешно сохранен",
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function saveAvatar()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$rules = [
			'file_base64' => ['required'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'file_base64' => 'Файл',
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
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}

		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$data = $contractor->data_json ? json_decode($contractor->data_json, true) : [];
		
		if (array_key_exists('avatar', $data)) {
			return $this->responseError('Файл уже существует', 400);
		}
		
		$replace = substr($this->request->file_base64, 0, strpos($this->request->file_base64, ',') + 1);
		$image = str_replace($replace, '', $this->request->file_base64);
		$image = str_replace(' ', '+', $image);
		$decodedImage = base64_decode($image, true);
		
		if (!base64_encode($decodedImage) === $image) {
			return $this->responseError('Файл не является Base64', 400);
		}

		$strlen = mb_strlen($image);

		// https://en.wikipedia.org/wiki/Base64#Padding
		$y = ($strlen - 2 == '=') ? 2 : 1;
		if ($strlen * 3 / 4 - $y > 1024 * 1024) {
			return $this->responseError('Размер файла не должен превышать 1 Мб', 400);
		}
		
		$fileName =  Str::uuid()->toString();
		try {
			$fileExt = explode('/', explode(':', substr($this->request->file_base64, 0, strpos($this->request->file_base64, ';')))[1])[1];
		} catch (Throwable $e) {
			return $this->responseError('Строка Base64 не содержит расширения файла', '400');
		}
		
		if (!Storage::put('contractor/avatar/' . $fileName . '.' . $fileExt, $decodedImage)) {
		//if (!$this->request->file('file')->storeAs('contractor/avatar', $fileName . '.' . $fileExt)) {
			return $this->responseError(null, 500);
		}

		$data['avatar'] = [
			'name' => $fileName,
			'ext' => $fileExt,
		];
		$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		if (!$contractor->save()) {
			return $this->responseError(null, 500);
		}
		
		$data = [
			'contractor' => $contractor->format(),
		];
		
		return $this->responseSuccess('Файл успешно сохранен', $data);
	}
	
	/**
	 * Avatar delete
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Файл успешно удален",
	 * 	"data": {
	 * 		"contractor": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city": "Москва",
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"is_new": false
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function deleteAvatar()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}

		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$data = $contractor->data_json ? json_decode($contractor->data_json, true) : [];
		
		if (!array_key_exists('avatar', $data)) {
			return $this->responseError('Файл не найден', 400);
		}
		
		unset($data['avatar']);
		$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		if (!$contractor->save()) {
			return $this->responseError(null, 500);
		}
		
		$data = [
			'contractor' => $contractor->format(),
		];
		
		return $this->responseSuccess('Файл успешно удален', $data);
	}
	
	/**
	 * Tariff type list
	 *
	 * @queryParam api_key string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 * 		{
	 * 			"tariff_type": {
	 * 				"id": 1,
	 * 				"name": "Regular",
	 * 				"alias": "regular",
	 * 				"description": null
	 * 			}
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariffTypes()
	{
		$tariffTypes = ProductType::where('is_tariff', true)
			->where('is_active', true)
			->get();
		
		if ($tariffTypes->isEmpty()) {
			return $this->responseError('Типы тарифов не найдены', 400);
		}
		
		$data = [];
		foreach ($tariffTypes ?? [] as $tariffType) {
			$data[] = [
				'tariff_type' =>  $tariffType->format(),
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Tariff list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @queryParam tariff_type_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"tariff": {
	 *				"id": 1,
	 *				"name": "Regular",
	 *				"duration": 30,
	 *				"price": 6300,
	 *				"is_hit": false,
	 * 				"is_unified": false,
	 *				"is_order_allow": true,
	 *				"is_certificate_allow": true,
	 *				"tariff_type": {
	 *					"id": 1,
	 *					"name": "Regular",
	 *					"alias": "regular",
	 *					"description": null
	 *				},
	 *				"employee": {
	 *					"id": 1,
	 * 					"name": "John Smith",
	 * 					"photo_file_path": null,
	 * 					"icon_file_path": null,
	 * 					"instagram": null
	 * 				},
	 *				"city": {
	 *					"id": 1,
	 *					"name": "Москва",
	 *				}
	 * 			}
	 * 		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariffs()
	{
		$tariffTypeId = $this->request->tariff_type_id;
		if (!$tariffTypeId) {
			return $this->responseError('Не передан ID типа тарифа', 400);
		}
		
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$tariffType = ProductType::where('is_tariff', true)
			->where('is_active', true)
			->find($tariffTypeId);
		if (!$tariffType) {
			return $this->responseError('Тип тарифа не найден', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}

		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city_id ?? 0;

		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}

		$tariffs = Product::where('product_type_id', $tariffTypeId)
			->whereIn('city_id', [$city->id, 0])
			->where('is_active', true)
			->get();
		
		$data = [];
		foreach ($tariffs ?? [] as $tariff) {
			$data[] = [
				'tariff' =>  $tariff->format(),
			];
		}
		
		if ($tariffs->isEmpty()) {
			return $this->responseError('Тарифы не найдены', 400);
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Tariff detailed
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @queryParam tariff_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 *		"tariff": {
	 *			"id": 1,
	 *			"name": "Regular",
	 *			"duration": 30,
	 *			"price": 6300,
	 *			"is_hit": false,
	 *			"is_unified": false,
	 *			"is_order_allow": true,
	 *			"is_certificate_allow": true,
	 *			"tariff_type": {
	 *				"id": 1,
	 *				"name": "Regular",
	 *				"alias": "regular",
	 *				"description": null
	 *			},
	 *			"employee": {
	 *				"id": 1,
	 *				"name": "John Smith",
	 *				"photo_file_path": null,
	 *				"icon_file_path": null,
	 *				"instagram": null
	 *			},
	 *			"city": {
	 *				"id": 1,
	 *				"name": "Москва"
	 *			}
	 *		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariff()
	{
		$tariffId = $this->request->tariff_id;
		if (!$tariffId) {
			return $this->responseError('Не передан ID тарифа', 400);
		}
		
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city_id ?? 0;
		
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}

		$tariff = Product::where('is_active', true)
			->find($tariffId);
		if (!$tariff) {
			return $this->responseError('Тариф не найден', 400);
		}

		$data = [
			'tariff' =>  $tariff->format(),
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Tariff Amount
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @queryParam tariff_id int required No-example
	 * @queryParam flight_at string No-example
	 * @queryParam is_unified bool Unified certificate. No-example
	 * @queryParam promocode_id int No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "",
	 * 	"data": {
	 * 		"amount": 5500,
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariffPrice()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$tariffId = $this->request->tariff_id;
		if (!$tariffId) {
			return $this->responseError('Не передан ID тарифа', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city ?? 0;
		
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}
		
		$tariff = Product::where('is_active', true)
			->whereIn('city_id', [])
			->find($tariffId);
		if (!$tariff) {
			return $this->responseError('Тариф не найден', 400);
		}
		
		if (!$tariff->productType) {
			return $this->responseError('Некорректный тип тарифа', 400);
		}
		
		if ($tariff->price <= 0) {
			return $this->responseError('Некорректная стоимость тарифа', 400);
		}
		
		$date = date('Y-m-d');
		
		if ($this->request->promocode_id) {
			$promocode = Promocode::whereIn('city_id', [$city->id, 0])
				->where('is_active', true)
				->where('active_from_at', '<=', $date)
				->where('active_to_at', '>=', $date)
				->find($this->request->promocode_id);
			if (!$promocode) {
				return $this->responseError('Промокод не найден', 400);
			}
		}

		$flightAt = $this->request->flight_at ?? date('d.m.Y');
		$isUnified = $this->request->is_unified ?? false;
		
		if (!$tariff->validateFlightDate($flightAt)) {
			return $this->responseError('Некорректная дата полета для выбранного тарифа', 400);
		}

		$price = $tariff->calculateProductPrice($contractor, $flightAt, $isUnified, $promocode ?? null);
		if ($price <= 0) {
			return $this->responseError('Некорректная стоимость тарифа', 400);
		}
		
		$data = [
			'amount' => $price,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * City list
	 *
	 * @queryParam api_key string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"city": {
	 *				"id": 1,
	 *				"name": "Москва"
	 * 			}
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getCities()
	{
		$cities = City::where('is_active', true)
			->get();
		
		if ($cities->isEmpty()) {
			return $this->responseError('Города не найдены', 400);
		}
		
		$data = [];
		foreach ($cities as $city) {
			$data[] = [
				'city' => $city->format(),
			];
		}

		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Location list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"location": {
	 *				"id": 1,
	 *				"name": "ТРК VEGAS Кунцево",
	 *				"address": null,
	 *				"working_hours": null,
	 *				"phone": null,
	 *				"email": null,
	 *				"map_link": null,
	 *				"skype": null,
	 *				"whatsapp": null,
	 *				"scheme_file_path": null
	 * 			}
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getLocations()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city ?? 0;
		if (!$cityId) {
			return $this->responseError('Город не найден', 400);
		}

		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}

		$locations = Location::where('city_id', $city->id)
			->where('is_active', true)
			->get();
		
		if ($locations->isEmpty()) {
			return $this->responseError('Локации не найдены', 400);
		}

		$data = [];
		foreach ($locations as $location) {
			$data[] = [
				'location' => $location->format(),
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Legal Entity list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"legal_entity": {
	 *				"id": 1,
	 *				"name": "ООО Компания",
	 *				"public_offer_file_path": null
	 * 			}
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getLegalEntities()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city ?? 0;
		if (!$cityId) {
			return $this->responseError('Город не найден', 400);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}

		$legalEntityIds = Location::where('city_id', $city->id)
			->where('is_active', true)
			->pluck('legal_entity_id')
			->all();
		
		$legalEntityIds = array_unique($legalEntityIds);
		
		$legalEntities = LegalEntity::whereIn('id', $legalEntityIds)
			->where('is_active', true)
			->get();
		
		if ($legalEntities->isEmpty()) {
			return $this->responseError('Юридические лица не найдены', 400);
		}
		
		$data = [];
		foreach ($legalEntities as $legalEntity) {
			$data[] = [
				'legal_entity' => $legalEntity->format(),
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Promo list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"promo": {
	 *				"id": 1,
	 *				"name": "Акция",
	 *				"preview_text": null,
	 *				"detail_text": null,
	 * 				"discount": {
						"value": 5,
	 * 					"is_fixed": false
	 * 				}
	 * 			}
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getPromos()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city ?? 0;
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}
		
		$promos = Promo::where('is_active', true)
			->whereIn('city_id', [$city->id, 0])
			->get();
		
		if ($promos->isEmpty()) {
			return $this->responseError('Акции не найдены', 400);
		}
		
		$data = [];
		/** @var Promo[] $promo */
		foreach ($promos as $promo) {
			$data[] = [
				'promo' => $promo->format(),
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Promo detailed
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam promo_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"promo": {
	 *			"id": 1,
	 *			"name": "Акция",
	 *			"preview_text": null,
	 *			"detail_text": null,
	 *			"discount": {
	 *				"value": 5,
	 *				"is_fixed": false
	 *			}
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getPromo()
	{
		$promoId = $this->request->promo_id;
		if (!$promoId) {
			return $this->responseError('Не передан ID акции', 400);
		}
		
		$promo = Promo::where('is_active', true)
			->find($promoId);
		if (!$promo) {
			return $this->responseError('Акция не найдена', 400);
		}
		
		$data = [
			'promo' => $promo->format(),
		];

		return $this->responseSuccess(null, $data);
	}

	/**
	 * Promocode Verify
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @queryParam promocode string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"id": 23,
	 *		"is_active": true,
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function verifyPromocode()
	{
		$number = $this->request->promocode;
		if (!$number) {
			return $this->responseError('Не передан промокод', 400);
		}
		
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city ?? 0;
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}

		$date = date('Y-m-d');
		
		$promocode = Promocode::where('number', $number)
			->whereIn('city_id', [$city->id, 0])
			->where('is_active', true)
			->where('active_from_at', '<=', $date)
			->where('active_to_at', '>=', $date)
			->first();
		if (!$promocode) {
			return $this->responseError('Промокод не найден', 400);
		}
		
		$data = [
			'id' => $promocode->id,
			'is_active' => $promocode->is_active,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Flight list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"flight": {
	 *				"flight": {
	 * 					"date": "2021-01-01",
	 * 					"time": "12:00:00"
	 *					"tariff": {
	 *						"id": 1,
	 *						"name": "Regular",
	 *						"duration": 30,
	 *						"price": 6300,
	 *						"is_hit": false,
	 * 						"is_unified": false,
	 *						"is_order_allow": true,
	 *						"is_certificate_allow": true,
	 *						"tariff_type": {
	 *							"id": 1,
	 *							"name": "Regular",
	 *							"alias": "regular",
	 *							"description": null
	 *						},
	 *						"employee": {
	 *							"id": 1,
	 * 							"name": "John Smith",
	 * 							"photo_file_path": null,
	 * 							"icon_file_path": null,
	 * 							"instagram": null
	 * 						},
	 *						"city": {
	 *							"id": 1,
	 *							"name": "Москва"
	 *						}
	 * 					},
	 *					"location": {
	 *						"id": 1,
	 *						"name": "ТРК VEGAS Кунцево",
	 *						"address": null,
	 *						"working_hours": null,
	 *						"phone": null,
	 *						"email": null,
	 *						"map_link": null,
	 *						"skype": null,
	 *						"whatsapp": null,
	 *						"scheme_file_path": null
	 * 					},
	 *					"score": "300"
	 * 				}
	 * 			}
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getFlights()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		/*$dealIds = Deal::where('contractor_id', $contractorId)
			->pluck('id');*/
		$dealPositions = DealPosition::whereRelation('deal', 'contractor_id', $contractorId)
				->whereRelation('status', 'alias', '=', 'calendar');
		$dealPositionIds = $dealPositions->pluck('id');
		$dealPositions = $dealPositions->get();

		$scores = Score::where('contractor_id', $contractorId)
			->whereIn('deal_position_id', $dealPositionIds)
			->get();

		$scoreData = [];
		foreach ($scores ?? [] as $score) {
			$scoreData[$score->deal_id] = $score->score;
		}
		
		$data = [];
		foreach ($dealPositions as $dealPosition) {
			$data[] = [
				'flight' => [
					'date' => Carbon::parse($dealPosition->flight_at)->format('Y-m-d'),
					'time' => Carbon::parse($dealPosition->flight_at)->format('H:i'),
					'tariff' =>  $dealPosition->product ? $dealPosition->product->format() : null,
					'location' =>  $dealPosition->location ? $dealPosition->location->format() : null,
					'score' =>  $scoreData[$dealPosition->id] ?? 0,
				],
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Order create
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @bodyParam name string required No-example
	 * @bodyParam phone string required +71234567890 No-example
	 * @bodyParam email string required No-example
	 * @bodyParam product_id int required No-example
	 * @bodyParam product_amount int required No-example
	 * @bodyParam is_certificate_order bool required No-example
	 * @bodyParam flight_date date No-example
	 * @bodyParam flight_time time No-example
	 * @bodyParam is_unified bool No-example
	 * @bodyParam location_id int No-example
	 * @bodyParam promocode_id string No-example
	 * @bodyParam certificate_id string No-example
	 * @bodyParam certificate_whom string For whom certificate. No-example
	 * @bodyParam comment string No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Профиль успешно сохранен",
	 * 	"data": {
	 * 		"order": {
	 * 			"id": 1,
	 * 			"name": "John",
	 * 			"lastname": "Smith",
	 * 			"email": "john.smith@gmail.com",
	 * 			"phone": null,
	 * 			"city_id": 1,
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой"
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function createorder()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$rules = [
			'is_certificate_order' => ['required', 'boolean'],
			'name' => ['required', 'min:3', 'max:50'],
			'phone' => ['required', 'valid_phone'],
			'email' => ['required', 'email'],
			'product_id' => ['required', 'numeric'],
			'product_amount' => ['required', 'numeric'],
			'flight_date' => ['required_if:is_certificate_order,false', 'date', 'after_or_equal:' . date('Y-m-d')],
			'flight_time' => ['required_if:is_certificate_order,false', 'date_format:H:i'],
			'is_unified' => ['required_if:is_certificate_order,true', 'boolean'],
			'location_id' => ['required_if:is_certificate_order,false', 'numeric'],
			'promocode_id' => ['sometimes', 'required', 'numeric'],
			'certificate_id' => ['sometimes', 'required_if:is_certificate_order,false', 'numeric'],
			'certificate_whom' => ['required_if:is_certificate_order,true', 'min:3', 'max:50'],
		];
		$validator = Validator::make($this->request->all(), $rules, Controller::API_VALIDATION_MESSAGES)
			->setAttributeNames([
				'is_certificate_order' => 'Тип заявки',
				'name' => 'Имя',
				'phone' => 'Номер телефона',
				'email' => 'E-mail',
				'product_id' => 'Позиция',
				'product_amount' => 'Стоимость',
				'flight_date' => 'Дата полета',
				'flight_time' => 'Время полета',
				'is_unified' => 'Единый сертификат',
				'location_id' => 'Локация',
				'promocode_id' => 'Промокод',
				'certificate_id' => 'Сертификат',
				'certificate_whom' => 'Для кого сертификат',
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
		
		if ($this->request->flight_date && $this->request->flight_time) {
			$flightDateCarbon = Carbon::parse($this->request->flight_date . ' ' . $this->request->flight_time);
			if ($flightDateCarbon->timestamp <= Carbon::now()->timestamp) {
				return $this->responseError('Некорректная дата и время полета', 400);
			}
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$productId = $this->request->product_id ?? 0;
		if (!$productId) {
			return $this->responseError('Не передан ID позиции', 400);
		}
		
		$productAmount = $this->request->product_amount ?? 0;
		if (!$productAmount) {
			return $this->responseError('Не передана стоимость позиции', 400);
		}
		
		$product = Product::where('is_active', true)
				->find($productId);
		if (!$product) {
			return $this->responseError('Позиция не найдена', 400);
		}
			
		// ToDo: пересчет стоимости позиции с учетом текущего ценообразования
		// ToDO: сообщение об ошибке, если цена не совпадет с полученной
		
		if ($this->request->location_id) {
			$location = Location::where('is_active', true)
				->find($this->request->location_id);
			if (!$location) {
				return $this->responseError('Локация не найдена', 400);
			}
		}
		
		$cityId = $contractor->city_id ?? 0;
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}
		
		$date = date('Y-m-d');
		
		$statusesData = HelpFunctions::getStatusesByType();
		
		if (!array_key_exists(Order::RECEIVED_STATUS, $statusesData['order'])) {
			return $this->responseError('Статус заявки не найден', 400);
		}
		
		if ($this->request->certificate_id && !$this->request->is_certificate_order) {
			if (!array_key_exists(Certificate::CREATED_STATUS, $statusesData['certificate'])) {
				return $this->responseError('Статус сертификата не найден', 400);
			}

			$certificate = Certificate::whereIn('city_id', [$city->id, 0])
				->where('status_id', $statusesData['certificate'][Certificate::CREATED_STATUS]['id'])
				->where('product_id', $product->id)
				->where(function ($query) use ($date) {
					$query->where('expire_at', '>=', $date)
						->orWhereNull('expire_at');
				})
				->find($this->request->certificate_id);
			if (!$certificate) {
				return $this->responseError('Сертификат не найден', 400);
			}
		}
		
		if ($this->request->promocode_id) {
			$promocode = Promocode::whereIn('city_id', [$city->id, 0])
				->where('is_active', true)
				->where('active_from_at', '<=', $date)
				->where(function ($query) use ($date) {
					$query->where('active_to_at', '>=', $date)
						->orWhereNull('active_to_at');
				})
				->find($this->request->promocode_id);
			if (!$promocode) {
				return $this->responseError('Промокод не найден', 400);
			}
		}
		
		try {
			\DB::beginTransaction();
			
			// создание сертификата
			if ($this->request->is_certificate_order) {
				$certificate = new Certificate();
				$certificate->status_id = $statusesData['certificate'][Certificate::CREATED_STATUS]['id'];
				$certificate->contractor_id = $contractor->id;
				$certificate->city_id = $city->id;
				$certificate->product_id = $product->id;
				$certificate->expire_at = Carbon::now()->addYear();
				$certificate->is_unified = $this->request->is_certificate_order ? $this->request->is_unified : 0;
				$certificate->save();
			}
			
			// создание заявки
			$order = new Order();
			$order->status_id = $statusesData['order'][Order::RECEIVED_STATUS]['id'];
			$order->contractor_id = $contractor->id;
			$order->name = $this->request->name;
			$order->phone = $this->request->phone;
			$order->email = $this->request->email;
			$order->city_id = $city->id;
			$order->product_id = $product->id;
			$order->amount = $productAmount ?? 0;
			$order->duration = $product->duration ?? 0;
			$order->promocode_id = (isset($promocode) && $promocode instanceof Promocode) ? $promocode->id : 0;
			$order->is_certificate_order = $this->request->is_certificate_order ?? 0;
			$order->certificate_id = (isset($certificate) && $certificate instanceof Certificate) ? $certificate->id : 0;
			
			$orderData = [];
			if (!$this->request->is_certificate_order) {
				$order->location_id = (isset($location) && $location instanceof Location) ? $location->id : 0;
				$order->flight_at = $flightDateCarbon->format('Y-m-d H:i');
			} else {
				$order->is_unified = $this->request->is_unified ?? 0;
				$orderData['certificate_whom'] = $this->request->certificate_whom;
			}
			$orderData['comment'] = $this->request->comment;
			$order->source = 'api';
			$order->data_json = $orderData;
			$order->save();

			// регистрация сертификата
			if ($this->request->certificate_id && !$this->request->is_certificate_order) {
				$certificate->status_id = $statusesData['certificate'][Certificate::REGISTERED_STATUS]['id'];
				$certificate->save();
			}
			
			$dealData = [];
			
			// создание сделки
			$deal = new Deal();
			$deal->contractor_id = $contractor->id;
			$deal->data_json = $dealData;
			$deal->save();
			
			// создание позиции сделки
			$dealPosition = new DealPosition();
			$dealPosition->deal_id = $deal->id;
			$dealPosition->status_id = $statusesData['deal'][DealPosition::CREATED_STATUS]['id'];
			$dealPosition->order_id = $order->id;
			$dealPosition->product_id = $product->id;
			$dealPosition->certificate_id = (isset($certificate) && $certificate instanceof Certificate) ? $certificate->id : 0;
			$dealPosition->duration = $product->duration ?? 0;
			$dealPosition->amount = $productAmount ?? 0;
			$dealPosition->city_id = $city->id;
			$dealPositionData = [];
			if (!$this->request->is_certificate_order) {
				$dealPosition->location_id = (isset($location) && $location instanceof Location) ? $location->id : 0;
				$dealPosition->flight_at = $flightDateCarbon->format('Y-m-d H:i');
			}
			$dealPositionData['comment'] = $this->request->comment;
			$dealPosition->data_json = $dealPositionData;
			$dealPosition->save();
			
			// создание счета
			$bill = new Bill();
			$bill->deal_id = $deal->id;
			$bill->deal_position_id = $dealPosition->id;
			$bill->status_id = $statusesData['bill'][Bill::NOT_PAYED_STATUS]['id'];
			$bill->amount = $productAmount ?? 0;
			$bill->save();
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			Log::debug($e);
			
			return $this->responseError(null, '500', $e->getMessage() . ' - ' . $this->request->url());
		}
		
		//dispatch(new \App\Jobs\SendOrderEmail($order));
		$job = new \App\Jobs\SendOrderEmail($order);
		$job->handle();
		
		$data = [
			'order' => $order->format(),
		];
		
		return $this->responseSuccess('Заявка успешно создана', $data);
	}
	
	/**
	 * Certificate Verify
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @queryParam number string required No-example
	 * @queryParam product_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"id": 15,
	 *		"number": "C123456",
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function verifyCertificate()
	{
		$number = $this->request->number;
		if (!$number) {
			return $this->responseError('Не передан номер сертификата', 400);
		}
		
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city_id ?? 0;
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}
		
		$productId = $this->request->product_id ?? 0;
		if (!$productId) {
			return $this->responseError('Не передан ID позиции', 400);
		}
		
		$product = Product::where('is_active', true)
			->find($productId);
		if (!$product) {
			return $this->responseError('Позиция не найдена', 400);
		}
		
		$statusesData = HelpFunctions::getStatusesByType();
		if (!array_key_exists(Certificate::CREATED_STATUS, $statusesData['certificate'])) {
			return $this->responseError('Статус сертификата не найден', 400);
		}
		
		$date = date('Y-m-d');
		
		$certificate = Certificate::where('number', $number)
			->whereIn('city_id', [$city->id, 0])
			->where('status_id', $statusesData['certificate'][Certificate::CREATED_STATUS]['id'])
			->where('product_id', $product->id)
			->where(function ($query) use ($date) {
				$query->where('expire_at', '>=', $date)
					->orWhereNull('expire_at');
			})
			->first();
		if (!$certificate) {
			return $this->responseError('Сертификат не найден', 400);
		}
		
		$data = [
			'id' => $certificate->id,
			'number' => $certificate->number,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Notification list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"notification": {
	 *				"id": 1,
	 *				"title": "Заголовок уведомления",
	 *				"description": "Описание уведомления",
	 *				"is_new": true,
	 * 				"created_at": "Y-m-d H:i:s",
	 * 				"updated_at": "Y-m-d H:i:s"
	 * 			}
	 * 		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getNotifications()
	{
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city_id ?? 0;
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}
		
		$notifications = Notification::where('is_active', true)
			->whereIn('contractor_id', [$contractor->id, 0])
			->whereIn('city_id', [$city->id, 0])
			->get();
		
		$data = [];
		foreach ($notifications ?? [] as $notification) {
			$data[] = [
				'notification' =>  $notification->format(),
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Notification detailed
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam token string required No-example
	 * @queryParam notification_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 *		"notification": {
	 *			"id": 1,
	 *			"title": "Заголовок уведомления",
	 *			"description": "Описание уведомления",
	 *			"is_new": true,
	 * 			"created_at": "Y-m-d H:i:s",
	 * 			"updated_at": "Y-m-d H:i:s"
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getNotification()
	{
		$notificationId = $this->request->notification_id ?? 0;
		if (!$notificationId) {
			return $this->responseError('Не передан ID уведомления', 400);
		}
		
		$authToken = $this->request->token ?? '';
		if (!$authToken) {
			return $this->responseError('Не передан токен авторизации', 400);
		}
		
		$token = HelpFunctions::validToken($authToken);
		if (!$token) {
			return $this->responseError('Токен не найден', 400);
		}
		
		$contractorId = $token->contractor_id ?? 0;
		if (!$contractorId) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$cityId = $contractor->city_id ?? 0;
		if ($cityId) {
			$city = City::where('is_active', true)
				->find($cityId);
			if (!$city) {
				return $this->responseError('Город не найден', 400);
			}
		}
		
		$notification = Notification::where('is_active', true)
			->whereIn('contractor_id', [$contractor->id, 0])
			->whereIn('city_id', [$city->id, 0])
			->find($notificationId);
		if (!$notification) {
			return $this->responseError('Уведомление не найдено', 400);
		}
		
		$data = [
			'notification' =>  $notification->format(),
		];
		
		// после прочтения уведомления снимаем признак того, что оно новое
		if ($notification->is_new) {
			$notification->is_new = false;
			$notification->save();
		}
		
		return $this->responseSuccess(null, $data);
	}
}