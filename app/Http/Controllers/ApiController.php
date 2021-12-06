<?php

namespace App\Http\Controllers;

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
use App\Models\MobAuth;
use App\Models\Code;
use App\Models\Promo;
use App\Models\TariffType;
use App\Models\Tariff;
use App\Models\Location;
use App\Models\Promocode;
use App\Models\Product;
use App\Models\Order;
use App\Models\Deal;

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
	 * 		"id": 1,
	 * 		"token": "328dda59f036efc26720937545efe01e",
	 * 	 	"contractor_id": 1,
	 * 	 	"created_at": "2021-11-12 18:36:05",
	 * 	 	"updated_at": "2021-11-12 18:36:05"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function login() {
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
		
		$mobAuth = new MobAuth();
		$mobAuth->contractor_id = $contractor->id;
		$mobAuth->setToken($contractor);
		
		if ($mobAuth->save()) {
			return $this->responseSuccess(null, $mobAuth->toArray());
		}
		
		return $this->responseError(null, 500);
	}
	
	/**
	 * Logout
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
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
	 * Verification code send
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam email string required No-example
	 * @queryParam type string Action type. For Registration - empty value, for Password Restore - "password_restore". No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Код подтверждения отправлен на john.smith@gmail.com",
	 * 	"data": {
	 * 		"contractor": {
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
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"last_auth_at": "2021-01-01 12:00:00",
	 * 			"created_at": "2021-01-01 12:00:00",
	 * 			"updated_at": "2021-01-01 12:00:00"
	 * 		},
	 * 		"code": {
	 * 			"id": 1,
	 * 			"code": 1234,
	 * 			"contractor_id": 1,
	 * 			"is_reset": 0,
	 * 			"reset_at": null,
	 * 			"created_at": "2021-11-12 18:36:05",
	 * 			"updated_at": "2021-11-12 18:36:05"
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
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
		
		$data = [
			'contractor' => $contractor ? $contractor->format() : [],
			'code' => $code->toArray(),
		];

		return $this->responseSuccess('Код подтверждения отправлен на ' . $email, $data);
	}
	
	/**
	 * Code verification
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
	 * 			"city_id": 1,
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"last_auth_at": "2021-01-01 12:00:00",
	 * 			"created_at": "2021-01-01 12:00:00",
	 * 			"updated_at": "2021-01-01 12:00:00"
	 * 		},
	 * 		"code": {
	 * 			"id": 1,
	 * 			"code": 1234,
	 * 			"contractor_id": 1,
	 * 			"is_reset": 0,
	 * 			"reset_at": null,
	 * 			"created_at": "2021-11-12 18:36:05",
	 * 			"updated_at": "2021-11-12 18:36:05"
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
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
		if (!$code->save()) {
			return $this->responseError(null, 500);
		}
		
		$contractor = $code->contractor_id ? Contractor::find($code->contractor_id) : null;
		
		$data = [
			'contractor' => $contractor ? $contractor->format() : [],
			'code' => $code->toArray(),
		];
		
		return $this->responseSuccess('Код подтвержден', $data);
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
	 * 			"city_id": 1,
	 * 			"discount": 5,
	 *			"birthdate": "1990-01-01",
	 * 			"avatar_file_base64": null,
	 * 			"flight_time": 100,
	 * 			"score": 10000,
	 * 			"status": "Золотой",
	 * 			"is_active": true,
	 * 			"last_auth_at": "2021-01-01 12:00:00",
	 * 			"created_at": "2021-01-01 12:00:00",
	 * 			"updated_at": "2021-01-01 12:00:00"
	 * 		},
	 * 		"mob_auth": {
	 * 			"id": 1,
	 * 			"contractor_id": 1,
	 * 			"token": "6136d60c36e6925bf98dea7e05d5f5c8",
	 * 			"created_at": "2021-11-12 18:36:05",
	 * 			"updated_at": "2021-11-12 18:36:05"
	 * 		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function register() {
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
			/*$data = [
				'birthdate' => Carbon::parse($this->request->birthdate)->format('Y-m-d'),
			];*/
			$contractor = new Contractor();
			$contractor->name = $this->request->name;
			$contractor->email = $this->request->email;
			$contractor->city_id = $this->request->city_id;
			$contractor->birthdate = Carbon::parse($this->request->birthdate)->format('Y-m-d');
			/*$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);*/
		}
		
		$contractor->password = $this->request->password;
		if (!$contractor->save()) {
			return $this->responseError(null, 500);
		}
		
		$mobAuth = new MobAuth();
		$mobAuth->contractor_id = $contractor->id;
		$mobAuth->setToken($contractor);
		if (!$mobAuth->save()) {
			return $this->responseError(null, 500);
		}
		
		$contractor->last_auth_at = date('Y-m-d H:i:s');
		$contractor->save();
		
		$data = [
			'contractor' => $contractor->format(),
			'mob_auth' => $mobAuth ? $mobAuth->toArray() : null,
		];

		return $this->responseSuccess('Регистрация успешно завершена', $data);
	}
	
	/**
	 * Password change
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @bodyParam password string required Password (md5). No-example
	 * @bodyParam password_confirmation string required Password confirmation (md5). No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Пароль успешно изменен",
	 * 	"data": {
	 * 		"id": 1,
	 * 		"name": "John",
	 * 		"lastname": "Smith",
	 * 		"email": "john.smith@gmail.com",
	 * 		"phone": null,
	 * 		"city_id": 1,
	 * 		"discount": 5,
	 *		"birthdate": "1990-01-01",
	 * 		"avatar_file_base64": null,
	 * 		"flight_time": 100,
	 * 		"score": 10000,
	 * 		"status": "Золотой",
	 * 		"is_active": true,
	 * 		"last_auth_at": "2021-01-01 12:00:00",
	 * 		"created_at": "2021-01-01 12:00:00",
	 * 		"updated_at": "2021-01-01 12:00:00"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"password": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
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
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$contractor->password = $password;
		if ($contractor->save()) {
			return $this->responseSuccess('Пароль успешно изменен', $contractor->format());
		}
		
		return $this->responseError(null, 500);
	}
	
	/**
	 * Profile
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"id": 1,
	 * 		"name": "John",
	 * 		"lastname": "Smith",
	 * 		"email": "john.smith@gmail.com",
	 * 		"phone": null,
	 * 		"city_id": 1,
	 * 		"discount": 5,
	 *		"birthdate": "1990-01-01",
	 * 		"avatar_file_base64": null,
	 * 		"flight_time": 100,
	 * 		"score": 10000,
	 * 		"status": "Золотой",
	 * 		"is_active": true,
	 * 		"last_auth_at": "2021-01-01 12:00:00",
	 * 		"created_at": "2021-01-01 12:00:00",
	 * 		"updated_at": "2021-01-01 12:00:00"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}

		return $this->responseSuccess(null, $contractor->format());
	}
	
	/**
	 * Profile save
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @bodyParam email string required No-example
	 * @bodyParam name string required No-example
	 * @bodyParam lastname string required No-example
	 * @bodyParam birthdate date required No-example
	 * @bodyParam phone string +71234567890 No-example
	 * @bodyParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Профиль успешно сохранен",
	 * 	"data": {
	 * 		"id": 1,
	 * 		"name": "John",
	 * 		"lastname": "Smith",
	 * 		"email": "john.smith@gmail.com",
	 * 		"phone": null,
	 * 		"city_id": 1,
	 * 		"discount": 5,
	 *		"birthdate": "1990-01-01",
	 * 		"avatar_file_base64": null,
	 * 		"flight_time": 100,
	 * 		"score": 10000,
	 * 		"status": "Золотой",
	 * 		"is_active": true,
	 * 		"last_auth_at": "2021-01-01 12:00:00",
	 * 		"created_at": "2021-01-01 12:00:00",
	 * 		"updated_at": "2021-01-01 12:00:00"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function saveProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$rules = [
			'name' => ['required', 'min:3', 'max:50'],
			'lastname' => ['required', 'min:3', 'max:50'],
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
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		/*$data = [
			'birthdate' => Carbon::parse($this->request->birthdate)->format('Y-m-d'),
		];*/
		$contractor->name = $this->request->name;
		$contractor->lastname = $this->request->lastname;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone;
		$contractor->city_id = $this->request->city_id;
		$contractor->birthdate = Carbon::parse($this->request->birthdate)->format('Y-m-d');
		/*$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);*/
		
		if ($contractor->save()) {
			return $this->responseSuccess('Профиль успешно сохранен', $contractor->format());
		}
		
		return $this->responseError(null, 500);
	}
	
	/**
	 * Profile delete
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
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
	public function deleteProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		if ($contractor->delete()) {
			return $this->responseSuccess('Профиль успешно удален');
		}
		
		return $this->responseError(null, 500);
	}
	
	/**
	 * Reset Profile
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Аккаунт контрагента успешно очищен",
	 * 	"data": {
	 *		"id": 1,
	 * 		"name": "John",
	 * 		"lastname": "Smith",
	 * 		"email": "john.smith@gmail.com",
	 * 		"phone": null,
	 * 		"city_id": 1,
	 * 		"discount": 5,
	 *		"birthdate": "1990-01-01",
	 * 		"avatar_file_base64": null,
	 * 		"flight_time": 100,
	 * 		"score": 10000,
	 * 		"status": "Золотой",
	 * 		"is_active": true,
	 * 		"last_auth_at": "2021-01-01 12:00:00",
	 * 		"created_at": "2021-01-01 12:00:00",
	 * 		"updated_at": "2021-01-01 12:00:00"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function resetProfile() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
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
		$contractor->discount = 0;
		$contractor->is_active = 1;
		$contractor->data_json = null;
		$contractor->last_auth_at = null;
		if ($contractor->save()) {
			return $this->responseSuccess('Аккаунт контрагента успешно очищен', $contractor->format());
		}
		
		return $this->responseError(null, 500);
	}

	/**
	 * Avatar save
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @bodyParam file_base64 string required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Файл успешно сохранен",
	 * 	"data": {
	 * 		"id": 1,
	 * 		"name": "John",
	 * 		"lastname": "Smith",
	 * 		"email": "john.smith@gmail.com",
	 * 		"phone": null,
	 * 		"city_id": 1,
	 * 		"discount": 5,
	 *		"birthdate": "1990-01-01",
	 * 		"avatar_file_base64": null,
	 * 		"flight_time": 100,
	 * 		"score": 10000,
	 * 		"status": "Золотой",
	 * 		"is_active": true,
	 * 		"last_auth_at": "2021-01-01 12:00:00",
	 * 		"created_at": "2021-01-01 12:00:00",
	 * 		"updated_at": "2021-01-01 12:00:00"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function saveAvatar() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
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
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$replace = substr($this->request->file_base64, 0, strpos($this->request->file_base64, ',') + 1);
		$image = str_replace($replace, '', $this->request->file_base64);
		$image = str_replace(' ', '+', $image);
		
		if (getimagesize($image) > 1024 * 1024) {
			return $this->responseError('Размер файла не должен превышать 1 Мб', 400);
		}
		
		$fileName =  Str::uuid()->toString();
		$fileExt = explode('/', explode(':', substr($this->request->file_base64, 0, strpos($this->request->file_base64, ';')))[1])[1];
		
		if (!Storage::put('contractor/avatar/' . $fileName . '.' . $fileExt, base64_decode($image))) {
		//if (!$this->request->file('file')->storeAs('contractor/avatar', $fileName . '.' . $fileExt)) {
			return $this->responseError(null, 500);
		}

		$data = json_decode($contractor->data_json, true);
		$data['avatar'] = [
			'name' => $fileName,
			'ext' => $fileExt,
		];
		$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		if ($contractor->save()) {
			return $this->responseSuccess('Файл успешно сохранен', $contractor->format());
		}
		
		return $this->responseError(null, 500);
	}
	
	/**
	 * Avatar delete
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "Файл успешно удален",
	 * 	"data": {
	 * 		"id": 1,
	 * 		"name": "John",
	 * 		"lastname": "Smith",
	 * 		"email": "john.smith@gmail.com",
	 * 		"phone": null,
	 * 		"city_id": 1,
	 * 		"discount": 5,
	 *		"birthdate": "1990-01-01",
	 * 		"avatar_file_base64": null,
	 * 		"flight_time": 100,
	 * 		"score": 10000,
	 * 		"status": "Золотой",
	 * 		"is_active": true,
	 * 		"last_auth_at": "2021-01-01 12:00:00",
	 * 		"created_at": "2021-01-01 12:00:00",
	 * 		"updated_at": "2021-01-01 12:00:00"
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function deleteAvatar() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$data = json_decode($contractor->data_json, true);
		
		if (!array_key_exists('avatar', $data)) {
			return $this->responseError('Файл не найден', 400);
		}
		
		unset($data['avatar']);
		$contractor->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		if ($contractor->save()) {
			return $this->responseSuccess('Файл успешно удален', $contractor->format());
		}
		
		return $this->responseError(null, 500);
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
	 * 			"id": 1,
	 * 			"name": "Regular",
	 * 			"data_json": {
	 * 			},
	 * 			"is_active": true,
	 * 			"created_at": "2021-01-01 12:00:00",
	 * 			"updated_at": "2021-01-01 12:00:00"
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariffTypes() {
		$tariffTypes = TariffType::where('is_active', true)
			->get();
		
		if ($tariffTypes->isEmpty()) {
			return $this->responseError('Типы тарифов не найдены', 400);
		}
		
		return $this->responseSuccess(null, $tariffTypes->toArray());
	}
	
	/**
	 * Tariff list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam tariff_type_id int required No-example
	 * @queryParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"tariff": {
	 *				"id": 1,
	 *				"name": "Regular",
	 *				"tariff_type_id": 1,
	 *				"employee_id": 10,
	 *				"city_id": 5,
	 *				"duration": 30,
	 *				"price": 6300,
	 *				"data_json": {
	 *				},
	 *				"is_active": true,
	 *				"is_hit": false,
	 *				"created_at": "2021-01-01 12:00:00",
	 *				"updated_at": "2021-01-01 12:00:00"
	 * 			}
	 *			"tariff_type": {
	 *				"id": 1,
	 *				"name": "Regular",
	 *				"data_json": {
	 *					"hint": "только будни",
	 *					"description": ""
	 *				},
	 *				"is_active": true,
	 *				"created_at": "2021-01-01 12:00:00",
	 *				"updated_at": "2021-01-01 12:00:00"
	 *			},
	 *			"employee": {
	 *				"id": 1,
	 * 				"name": "John Smith",
	 * 				"photo_path": null,
	 * 				"icon_path": null,
	 * 				"instagram": null
	 * 			},
	 *			"city": {
	 *				"id": 1,
	 *				"name": "Москва",
	 *				"is_active": true,
	 *				"created_at": "2021-10-01 18:23:27",
	 *				"updated_at": "2021-10-05 18:23:41"
	 *			}
	 * 		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariffs() {
		$tariffTypeId = $this->request->tariff_type_id;
		if (!$tariffTypeId) {
			return $this->responseError('Не передан ID типа тарифа', 400);
		}
		
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$tariffType = TariffType::where('is_active', true)
			->find($tariffTypeId);
		if (!$tariffType) {
			return $this->responseError('Тип тарифа не найден', 400);
		}

		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}

		$tariffs = Tariff::where('tariff_type_id', $tariffTypeId)
			->whereIn('city_id', [$cityId, 0])
			->where('is_active', true)
			->get();
		
		$data = [];
		foreach ($tariffs ?? [] as $tariff) {
			/** @var Tariff $tariff */
			$data[] = [
				'tariff' =>  $tariff->toArray(),
				'tariff_type' =>  $tariff->tariffType ? $tariff->tariffType->toArray() : null,
				'employee' => $tariff->employee ? $tariff->employee->format() : null,
				'city' => $tariff->city ? $tariff->city->toArray() : null,
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
	 * @queryParam tariff_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 * 		"tariff": {
	 *			"id": 1,
	 *			"name": "Regular",
	 *			"tariff_type_id": 1,
	 *			"employee_id": 10,
	 *			"city_id": 5,
	 *			"duration": 30,
	 *			"price": 6300,
	 *			"data_json": {
	 *			},
	 *			"is_active": true,
	 *			"is_hit": false,
	 *			"created_at": "2021-01-01 12:00:00",
	 *			"updated_at": "2021-01-01 12:00:00"
	 * 		},
	 *		"tariff_type": {
	 *			"id": 1,
	 *			"name": "Regular",
	 *			"data_json": {
	 *				"hint": "только будни",
	 *				"description": ""
	 *			},
	 *			"is_active": true,
	 *			"created_at": "2021-01-01 12:00:00",
	 *			"updated_at": "2021-01-01 12:00:00"
	 *		},
	 *		"employee": {
	 *			"id": 1,
	 * 			"name": "John Smith",
	 * 			"photo_path": null,
	 * 			"icon_path": null,
	 * 			"instagram": null
	 * 		},
	 *		"city": {
	 *			"id": 1,
	 *			"name": "Москва",
	 *			"is_active": true,
	 *			"created_at": "2021-10-01 18:23:27",
	 *			"updated_at": "2021-10-05 18:23:41"
	 *		}
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariff() {
		$tariffId = $this->request->tariff_id;
		if (!$tariffId) {
			return $this->responseError('Не передан ID тарифа', 400);
		}
		
		$tariff = Tariff::where('is_active', true)
			->find($tariffId);
		if (!$tariff) {
			return $this->responseError('Тариф не найден', 400);
		}

		$data = [
			'tariff' =>  $tariff->toArray(),
			'tariff_type' =>  $tariff->tariffType ? $tariff->tariffType->toArray() : null,
			'employee' => $tariff->employee ? $tariff->employee->format() : null,
			'city' => $tariff->city ? $tariff->city->toArray() : null,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Tariff Price
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int required No-example
	 * @queryParam tariff_id int required No-example
	 * @queryParam flight_at string No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": "",
	 * 	"data": {
	 * 		"price": 5500,
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": {"email": "Обязательно для заполнения"}, "debug": null}
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getTariffPrice() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$tariffId = $this->request->tariff_id;
		if (!$tariffId) {
			return $this->responseError('Не передан ID тарифа', 400);
		}

		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$tariff = Tariff::where('is_active', true)
			->find($tariffId);
		if (!$tariff) {
			return $this->responseError('Тариф не найден', 400);
		}
		
		$flightAt = $this->request->flight_at;
		
		$price = $tariff->price ?: 0;
		
		if ($price && $contractor->discount) {
			$price = round($price - $price * $contractor->discount / 100);
		}
		
		$data = [
			'price' => $price,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	/**
	 * Product list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 * 			"product": {
	 *				"id": 1,
	 *				"name": "Видеозапись",
	 *				"city_id": 1,
	 *				"price": 500,
	 *				"data_json": {
	 *				},
	 *				"is_active": true,
	 *				"created_at": "2021-01-01 12:00:00",
	 *				"updated_at": "2021-01-01 12:00:00"
	 * 			}
	 *			"city": {
	 *				"id": 1,
	 *				"name": "Москва",
	 *				"is_active": true,
	 *				"created_at": "2021-10-01 18:23:27",
	 *				"updated_at": "2021-10-05 18:23:41"
	 *			}
	 * 		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getProducts() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}

		$products = Product::whereIn('city_id', [$cityId, 0])
			->where('is_active', true)
			->get();
		
		$data = [];
		foreach ($products ?? [] as $product) {
			/** @var Product $product */
			$data[] = [
				'product' =>  $product->toArray(),
				'city' =>  $product->city ? $product->city->toArray() : null,
			];
		}
		
		if ($products->isEmpty()) {
			return $this->responseError('Продукты не найдены', 400);
		}
		
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
	 *			"id": 1,
	 *			"name": "Москва",
	 *			"is_active": true,
	 *			"created_at": "2021-01-01 12:00:00",
	 *			"updated_at": "2021-01-01 12:00:00",
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getCities() {
		$cities = City::where('is_active', true)
			->get();
		
		if ($cities->isEmpty()) {
			return $this->responseError('Города не найдены', 400);
		}
		
		return $this->responseSuccess(null, $cities->toArray());
	}
	
	/**
	 * Location list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 *			"id": 1,
	 *			"name": "ТРК VEGAS Кунцево",
	 *			"legal_entity_id": 1,
	 *			"data_json": {
	 *				"address": "",
	 *				"working_hours": "",
	 *				"phone": "",
	 *				"email": "",
	 *				"map_link": "",
	 *				"skype": "",
	 *				"whatsapp": "",
	 *				"scheme_file_path": ""
	 *			}
	 *			"is_active": true,
	 *			"created_at": "2021-01-01 12:00:00",
	 *			"updated_at": "2021-01-01 12:00:00",
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getLocations() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}

		$locations = Location::where('city_id', $cityId)
			->where('is_active', true)
			->get();
		
		if ($locations->isEmpty()) {
			return $this->responseError('Локации не найдены', 400);
		}
		
		return $this->responseSuccess(null, $locations->toArray());
	}
	
	/**
	 * Legal Entity list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 *			"id": 1,
	 *			"name": "ООО Компания",
	 *			"public_offer": null,
	 *			"created_at": "2021-01-01 12:00:00",
	 *			"updated_at": "2021-01-01 12:00:00",
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getLegalEntities() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}
		
		$legalEntityIds = Location::where('city_id', $cityId)
			->where('is_active', true)
			->pluck('legal_entity_id')
			->all();
		
		$legalEntityIds = array_unique($legalEntityIds);
		
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
				'public_offer' => ($legalEntity->data_json && array_key_exists('public_offer', $legalEntity->data_json)) ? \URL::to('/upload/public_offer/' . $legalEntity->data_json['public_offer']['name'] . '.' . $legalEntity->data_json['public_offer']['ext']) : null,
				'created_at' => $legalEntity->created_at ? Carbon::parse($legalEntity->created_at)->format('Y-m-d H:i:s') : null,
				'updated_at' => $legalEntity->updated_at ? Carbon::parse($legalEntity->updated_at)->format('Y-m-d H:i:s') : null,
			];
		}
		
		return $this->responseSuccess(null, $legalEntitiesData);
	}
	
	/**
	 * Promo list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam city_id int No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 *			"id": 1,
	 *			"name": "Акция",
	 *			"preview_text": "",
	 *			"detail_text": "",
	 *			"city_id": 1,
	 *			"is_active": true,
	 *			"created_at": "2021-01-01 12:00:00",
	 *			"updated_at": "2021-01-01 12:00:00",
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getPromos() {
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}
		
		$promos = Promo::where('city_id', $cityId)
			->where('is_active', true)
			->get();
		
		if ($promos->isEmpty()) {
			return $this->responseError('Акции не найдены', 400);
		}
		
		return $this->responseSuccess(null, $promos->toArray());
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
	 *		"id": 1,
	 *		"name": "Акция",
	 *		"preview_text": "",
	 *		"detail_text": "",
	 *		"city_id": 1,
	 *		"is_active": true,
	 *		"created_at": "2021-01-01 12:00:00",
	 *		"updated_at": "2021-01-01 12:00:00",
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getPromo() {
		$promoId = $this->request->promo_id;
		if (!$promoId) {
			return $this->responseError('Не передан ID акции', 400);
		}
		
		$promo = Promo::where('is_active', true)
			->find($promoId);
		if (!$promo) {
			return $this->responseError('Акция не найдена', 400);
		}
		
		return $this->responseSuccess(null, $promo->toArray());
	}

	/**
	 * Promocode Verify
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam promocode string required No-example
	 * @queryParam city_id int required No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": {
	 *		"is_active": true,
	 * 	}
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function verifyPromocode() {
		$number = $this->request->promocode;
		if (!$number) {
			return $this->responseError('Не передан промокод', 400);
		}
		
		$cityId = $this->request->city_id;
		if (!$cityId) {
			return $this->responseError('Не передан ID города', 400);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if (!$city) {
			return $this->responseError('Город не найден', 400);
		}
		
		$date = date('Y-m-d');
		
		$promocode = Promocode::where('number', $number)
			->whereIn('city_id', [$cityId, 0])
			->where('is_active', true)
			->where('active_from_at', '<=', $date)
			->where('active_to_at', '>=', $date)
			->first();
		if (!$promocode) {
			return $this->responseError('Промокод не найден', 400);
		}
		
		$data = [
			'is_active' => $promocode->is_active,
		];
		
		return $this->responseSuccess(null, $data);
	}
	
	public function verifyCertificate() {
	}
	
	public function getNotifications() {
	}
	
	public function getNotification() {
	}
	
	/**
	 * Flight list
	 *
	 * @queryParam api_key string required No-example
	 * @queryParam contractor_id int No-example
	 * @response scenario=success {
	 * 	"success": true,
	 * 	"message": null,
	 * 	"data": [
	 *		{
	 *			"flight_at": "2021-01-01 12:00:00",
	 *			"duration": "30",
	 *			"scores": "300",
	 *		}
	 * 	]
	 * }
	 * @response status=400 scenario="Bad Request" {"success": false, "error": "Некорректный Api-ключ", "debug": null}
	 * @response status=404 scenario="Resource Not Found" {"success": false, "error": "Ресурс не найден", "debug": "<app_url>/api/<method>"}
	 * @response status=405 scenario="Method Not Allowed" {"success": false, "error": "Метод не разрешен", "debug": "<app_url>/api/<method>"}
	 * @response status=500 scenario="Internal Server Error" {"success": false, "error": "Внутренняя ошибка", "debug": "<app_url>/api/<method>"}
	 */
	public function getFlights() {
		$contractorId = $this->request->contractor_id;
		if (!$contractorId) {
			return $this->responseError('Не передан ID контрагента', 400);
		}
		
		$contractor = Contractor::where('is_active', true)
			->find($contractorId);
		if (!$contractor) {
			return $this->responseError('Контрагент не найден', 400);
		}
		
		$flights = Deal::where('contractor_id', $contractorId)
			->get();
		
		$data = [];
		foreach ($flights ?? [] as $flight) {
			$data[] = [
				'flight_at' => $flight['flight_at'],
				'duration' => $flight['duration'],
			];
		}
		
		return $this->responseSuccess(null, $data);
	}
}