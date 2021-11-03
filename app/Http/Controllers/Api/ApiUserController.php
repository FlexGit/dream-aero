<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\ApiModels\ApiHelperTrait;
use App\Models\Contractor;
use App\Models\MobAuth;
use Illuminate\Http\Request;
use Validator;

class ApiUserController extends Controller
{
	//use ApiHelperTrait;

	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/login",
	 *     operationId="auth",
	 *     tags={"auth"},
	 *     summary="Авторизация по email",
	 *     description="Получение токена авторизации",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @OA\Parameter(
	 *         name="login",
	 *         in="formData",
	 *         description="Логин (email)",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="password",
	 *         in="formData",
	 *         description="Пароль (md5)",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Успешная авторизация",
	 *     	   @OA\Schema(ref="#/definitions/ApiAuthModel"),
	 *     ),
	 *     @OA\Response(
	 *         response="default",
	 *         description="Ошибка",
	 *         @OA\Schema(ref="#/definitions/ApiErrorOrErrors")
	 *     ),
	 * )
	 */
	public function auth() {
		$rules = [
			'login' => 'required|string',
			'password' => 'required|string',
		];
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'login' => 'Наименование',
				'password' => 'Пароль',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$contractor = Contractor::where('email', $this->request->login)
			->first();
		if (!$contractor) {
			return response()->json(['status' => 'error', 'reason' => 'Пользователя с указанным E-mail не существует']);
		}

		if ($contractor->password !== $this->request->password) {
			return response()->json(['status' => 'error', 'reason' => 'Пароль указан неверно']);
		}

		$mobAuth = new MobAuth();
		$mobAuth->contractor_id = $contractor->id;
		$mobAuth->setToken($contractor);

		if ($mobAuth->save()) {
			$auth = new ApiAuthModel($mobAuth->token, ApiUserModel::makeFromUser($contractor));
			return response()->json($auth);
		}

		return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно пройти авторизацию, попробуйте позже']);
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/register",
	 *     operationId="register",
	 *     tags={"register"},
	 *     summary="Регистрация",
	 *     description="Регистрация контрагента",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @OA\Parameter(
	 *         name="name",
	 *         in="formData",
	 *         description="Имя",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="phone",
	 *         in="formData",
	 *         description="Телефон",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="email",
	 *         in="formData",
	 *         description="E-mail",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="password",
	 *         in="formData",
	 *         description="Пароль (md5)",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
	 *         name="city",
	 *         in="formData",
	 *         description="Город",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Успешная авторизация",
	 *     	   @OA\Schema(ref="#/definitions/ApiAuthModel"),
	 *     ),
	 *     @OA\Response(
	 *         response="default",
	 *         description="Ошибка",
	 *         @OA\Schema(ref="#/definitions/ApiErrorOrErrors")
	 *     ),
	 * )
	 */
	public function register() {

	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/register_request",
	 *     operationId="register_request",
	 *     tags={"register"},
	 *     summary="Запрос на регистрацию",
	 *     description="Запрос на регистрацию",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @OA\Parameter(
	 *         name="email",
	 *         in="formData",
	 *         description="E-mail",
	 *         required=true,
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Запрос на регистрацию подтвержден",
	 *     	   @OA\Schema(ref="#/definitions/ApiAuthModel"),
	 *     ),
	 *     @OA\Response(
	 *         response="default",
	 *         description="Ошибка",
	 *         @OA\Schema(ref="#/definitions/ApiErrorOrErrors")
	 *     ),
	 * )
	 */
	public function registerAttempt() {

	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/code_request",
	 *     operationId="code_request",
	 *     tags={"code"},
	 *     summary="Запрос кода подтверждения",
	 *     description="Запрос кода подтверждения",
	 *     consumes={"application/json"},
	 *     produces={"application/json"},
	 *     @OA\Parameter(
	 *         name="contractor_id",
	 *         in="formData",
	 *         description="ID контрагента",
	 *         required=true,
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Код подтверждения успешно отправлен",
	 *     	   @OA\Schema(ref="#/definitions/ApiAuthModel"),
	 *     ),
	 *     @OA\Response(
	 *         response="default",
	 *         description="Ошибка",
	 *         @OA\Schema(ref="#/definitions/ApiErrorOrErrors")
	 *     ),
	 * )
	 */
	public function sendCode() {

	}

	public function resendCode() {

	}

	public function verifyCode() {

	}

	public function restorePassword() {

	}

	public function confirmPassword() {

	}

	public function getProfile() {

	}

	public function saveProfile() {

	}

	public function saveAvatar() {

	}

	public function deleteAvatar() {

	}

	public function logout() {

	}

	public function getNotificationList() {

	}

	public function getNotification() {

	}

	public function deleteProfile() {

	}

	public function getFlights() {

	}
}