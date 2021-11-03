<?php

namespace App\ApiModels;

use App\Services\ApiHelpFunctions;
use App\Services\HelpFunctions;
use Illuminate\Http\Request;

trait ApiHelperTrait {
	/** @var \App\MobAuth|null -- текущий пользователь, определённый по токену, если был вызов getUserAuthByToken() */
	protected $reqUser = null;
	protected $requestSingleError = null;
	protected $requestFieldErrors = [];
	protected $reqAPIKey = null;

	/**
	 * Получает запись о пользователе
	 * @param Request $request
	 * @param bool $noError
	 * @return \App\MobAuth|null
	 */
	public function getUserAuthByToken (Request $request, $noError = false) {
		$token = $request->get('token');
		$userAuthByToken = ApiHelpFunctions::tokenVerification($token);
		if (is_string($userAuthByToken)) {
			if (!$noError) $this->setSingleError($userAuthByToken);
			$this->reqUser = null;
		} else {
			$this->reqUser = $userAuthByToken;
			Localization::setLocaleBy($this->reqUser->user);
		}
		return $this->reqUser;
	}

	/**
	 * @param Request $request
	 * @return null|string
	 */
	public function getReqAPIKey(Request $request) {
		if ($this->reqAPIKey) return $this->reqAPIKey;

		$apikey = $request->header('Osmotr-apikey');
		// TODO: удалить проверку на common, принять решение о том как должны работать приложения с ключем common
		// TODO: некоторые проверки в коде считают, что reqAPIKey не задан для viewapp (common)
		if ($apikey && $apikey !== 'common') {
			$this->reqAPIKey = strval($apikey);
		} else {
			$this->reqAPIKey = null;
		}
		return $this->reqAPIKey;
	}

	/**
	 * Проверяет входящие параметры и фиксирует ошибки, если параметры не отвечают
	 * условиям проверки. Проверка по каждому полю идёт до первой ошибки. Варианты
	 * вызова:
	 *
	 *    $this->requiredRequestParams([
	 *       'login' => ['emptyFieldError' => ['field' => 'phone', 'reason' => 'Логин задан некорректно!']],
	 *       'password' => ['emptyFieldError' => 'Пароль обязателен для заполнения!'],
	 *       'phone' => [
	 *          'emptyFieldError' => 'Телефон обязателен для заполнения!',
	 *          'phoneValidFieldError' => 'Телефон указан в неправильном формате!',
	 *       ],
	 *       'insure' => ['emptySingleError' => 'Не хватает данных для сохранения'],
	 *    ], $request);
	 *
	 *    emptyFieldError - проверка поля на empty, запись ошибки в $requestFieldErrors
	 *    phoneValidFieldError - проверка поля через HelpFunctions::phoneValidate, запись ошибки в $requestFieldErrors
	 *    emptySingleError - проверка поля на empty, запись ошибки в $requestSingleError
	 *
	 * @param array $params
	 * @param Request $request
	 * @param bool $jsonContentType
	 */
	protected function requiredRequestParams (array $params, Request $request, $jsonContentType = false) {
		foreach ($params as $field => $conditions) {
			if ($jsonContentType) {
				$val = $request->input($field);
			} else {
				$val = $request->get($field);
			}

			foreach ($conditions as $type => $error) {
				$hasError = false;
				$isFieldError = false;
				$defaultReason = '';

				// определяем, есть ли ошибка и какой у неё тип, если ошибка есть
				switch ($type) {
					case 'emptyFieldError':
						$hasError = empty($val);
						$isFieldError = true;
						$defaultReason = 'Поле не должно быть пустым';
						break;
					case 'phoneValidFieldError':
						$hasError = (HelpFunctions::phoneValidate($val) === false);
						$isFieldError = true;
						$defaultReason = 'Телефон задан в неверном формате';
						break;
					case 'emptySingleError':
						$hasError = empty($val);
						$isFieldError = false;
						$defaultReason = 'Поле не должно быть пустым';
						break;
				}// switch

				// ошибка по полю формы
				if ($hasError && $isFieldError) {
					if (is_string($error)) {
						$this->addFieldError($field, $error);
					} else if (!empty($error['field']) && !empty($error['reason'])) {
						$this->addFieldError($error['field'], $error['reason']);
					} else if (!empty($error['reason'])) {
						$this->addFieldError($field, $error['reason']);
					} else if (!empty($error['field'])) {
						$this->addFieldError($error['field'], $defaultReason);
					} else {
						$this->addFieldError($field, $defaultReason);
					}
				}// if error
				// одиночная ошибка
				else if ($hasError && !$isFieldError) {
					$this->setSingleError(is_string($error) ? $error : $defaultReason);
				}// if error

				// по каждому полю достаточно одной ошибки и не нужно проверять все $conditions
				if ($hasError) break;
			}// foreach condition
		}// foreach param
	}

	/**
	 * Фиксирует одиночную ошибку
	 *
	 * @param string $reason
	 * @return $this
	 */
	protected function setSingleError ($reason) {
		$this->requestSingleError = $reason;
		return $this;
	}

	/**
	 * Фиксирует одиночную ошибку и возвращает сам ответ
	 *
	 * @param string $reason
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseSingleError ($reason) {
		return $this->responseApiError($reason);
	}

	/**
	 * Фиксирует одиночную ошибку и возвращает сам ответ
	 *
	 * @param string $reason
	 * @param int $httpCode
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseSingleErrorHTTPCode ($reason, $httpCode = 200) {
		return $this->responseSingleErrorCode($reason, null, $httpCode);
	}

	/**
	 * Фиксирует одиночную ошибку и возвращает сам ответ
	 *
	 * @param string $reason
	 * @param string|null $code
	 * @param int $httpCode
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseSingleErrorCode ($reason, $code = null, $httpCode = 200) {
		if (!empty($reason)) $this->setSingleError($reason);
		$error = new ApiError($reason, $code);
		return response()->json($error, $httpCode);
	}

	/**
	 * Фиксирует одиночную ошибку и возвращает сам ответ
	 *
	 * @param string $reason
	 * @param int $httpCode
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseSingleErrorInvalidParams ($reason, $httpCode = 200) {
		return $this->responseSingleErrorCode($reason, 'INVALID_PARAMS', $httpCode);
	}

	/**
	 * Фиксирует сообщение об ошибке в поле формы с отсылкой к этому полю
	 *
	 * @param string $field
	 * @param string $reason
	 * @return $this
	 */
	protected function addFieldError ($field, $reason) {
		$this->requestFieldErrors[] = ['field' => $field, 'reason' => $reason];
		return $this;
	}

	/**
	 * Фиксирует сообщение об ошибке в поле формы и возвращает сам ответ
	 *
	 * @param string $field
	 * @param string $reason
	 * @param int $httpCode
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseFieldError ($field, $reason, $httpCode = 200) {
		$this->addFieldError($field, $reason);
		return $this->responseApiError(null, $httpCode);
	}

	/**
	 * Проверяет, есть ли запись об ошибках
	 *
	 * @return bool
	 */
	protected function anyError() {
		return !(empty($this->requestSingleError) && empty($this->requestFieldErrors));
	}

	/**
	 * Возвращает ошибку в виде json по формату: http://git.chedev.ru/asko/server/wikis/errors
	 *
	 * Приоритет формирования сообщения об ошибке / ошибках:
	 * 1) Если задан текст одиночной ошибки ($singleReason или $this->requestSingleError),
	 *    возвращается одиночная ошибка
	 * 2) Если одиночная ошибка не задана, возвращается ошибка про поля (даже если ничего
	 *    не накопилось)
	 *
	 * Лучший вариант использования:
	 *
	 *    if ($this->anyError()) return $this->responseApiError();
	 *
	 * @param string|null $singleReason -- принудительный текст одиночной ошибки
	 * @param int $code -- код HTTP-ответа
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseApiError($singleReason = null, $code = 200) {
		if (!empty($singleReason)) $this->setSingleError($singleReason);
		if (!empty($this->requestSingleError)) {
			$error = new ApiError($this->requestSingleError);
		} else {
			$error = new ApiErrors($this->requestFieldErrors);
		}
		return response()->json($error, $code);
	}

}
