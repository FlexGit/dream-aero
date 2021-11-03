<?php

namespace App\ApiModels;

use ReflectionClass;

/**
 * @SWG\Definition()
 */
class ApiError {
	const RETRY_LATER_OPERATION = 'В данный момент невозможно выполнить операцию, попробуйте позже!';
	const RETRY_LATER_DB = 'Не удалось получить данные из базы, попробуйте позже!';
	const WRONG_DB_DATA = 'В базе нет указанных данных!';
	const ACCESS_DENIED = 'Недостаточно прав!';
	const DATA_NOT_ALLOWED = 'Нельзя получить доступ к выбранным данным (обратитесь к администратору)';
	const INVALID_PARAMS = 'Неправильные входящие данные. Проверьте формат полей запроса и все ли нужные параметры заданы';
	const INVALID_PROCESS_TYPE = 'Выбран неправильный тип процесса: тип не относится к схеме осмотра или для него не определены шаги, или не заданы другие параметры';
	const INVALID_STEP = 'Выбран неправильный шаг: шаг не относится к процессу по схеме осмотра или загружаемый материал не подходит под настройки шага';
	const WRONG_APP = 'Вы используете приложение на платформе ViewApp. Запрашиваемая функция недоступна в данной версии приложения. Возможно, нужно скачать другое';
	const NOT_AUTHORIZED = 'Закончилось время действия сессии. Пожалуйста, выйдите через меню "Выйти", и авторизуйтесь заново';
	const NOT_AUTHORIZED_INTEGRAPI = 'Ошибка авторизации. Авторизуйтесь заново.';
	const MOBAPI_EXCEPTION = 'Что-то пошло не так, произошла ошибка. Попробуйте повторить действие через некоторое время. Если повтор не поможет, сообщите, пожалуйста, о проблеме в техническую поддержку';
	const INTEGRAPI_EXCEPTION = 'Код вернул Exception. Пожалуйста, сообщите в поддержку о проблеме. Проверьте параметры запроса, возможно какие-то из них приводят к ошибке';

	const CODE_RESEND_TOO_EARLY = 'Прошло недостаточное количество времени, попробуйте запросить новый код позже!';
	const CODE_RESEND_TOO_LATE = 'Попытка устарела, попробуйте выполнить действие с самого начала!';
	const CODE_RESEND_TOO_MANY_TRIES = 'Слишком много попыток отправить код, попробуйте выполнить действие с самого начала после некоторой паузы!';

	const INSPECTION_WRONG_CONDITIONS_STATUS = 'Неподходящий статус осмотра для внесения данных изменений';
	const DUPLICATE_UUID = 'Запись с таким uuid уже существует!';

	const WRONG_CONDITIONS = 'В текущий момент соблюдены не все требуемые условия. Попробуйте повторить действие позже';
	const WRONG_SERVICE_OPTIONS = 'Настройки схемы не позволяют выполнить затребованное действие';

	const WRONG_FIELD_ROLE = 'Поле Роль задано неправильно!';
	const WRONG_FIELD_PHONE = 'Поле Мобильный телефон задано некорректно!';
	const NEED_PHONE_OR_EMAIL = 'Необходимо указать телефон или email, иначе пользователь не сможет авторизоваться в системе';
	const NEED_PHONE_COMMON = 'Необходимо указать телефон, иначе не будет возможности авторизоваться в системе';
	const DUPLICATE_EMAIL = 'Пользователь с таким email уже зарегистрирован!';
	const DUPLICATE_PHONE = 'Пользователь с таким телефонным номером уже зарегистрирован!';
	
	const UNCREATABLE_BY_TEMPLATE = 'Создание нового осмотра по схеме недоступно, обратитесь к администратору - владельцу схемы';
	const UNEDITABLE_SERVICE = 'Редактирование данной схемы недоступно, обратитесь к администратору - владельцу схемы';
	const UNEDITABLE_PROCESS = 'Редактирование процесса недоступно, обратитесь к администратору - владельцу схемы';
	const UNEDITABLE_STEP = 'Редактирование шага недоступно, обратитесь к администратору - владельцу схемы';
	const UNCOPYABLE_SERVICE = 'Копирование схемы недоступно, обратитесь к администратору - владельцу схемы';

	const MOBAPI_NO_USER = 'Вы не авторизованы. Попробуйте повторно зайти в приложение и повторить действие';
	const MOBAPI_CREATE_BY_EXTERNAL_PARAMS_FAIL = 'Недостаточно параметров для создания нового осмотра. Попробуйте повторить действие или обратитесь за технической поддержкой';

	const STEP_FILE_MAX_LIMIT = 'Превышено максимальное ограничение на количество файлов для шага';

	const SHARED_ACCESS_GROUP_ACCESS_DENIED = 'Ограничение доступа для партнерской группы доступа';

	const QUERY_TOO_SHORT = 'Слишком короткий текст для поиска. Укажите номер телефона (минимум 5 цифр) или фамилию целиком.';
	const OUTDATED_DATA = 'Устаревшие результаты поиска.';

	const STATUS_WRONG_ID = 'Не удалось найти статус с указанным id';
	const STATUS_INACCESSIBLE = 'Выбранный статус недоступен. Возможно, осмотр уже был изменён. Попробуйте обновить данные на своей стороне и повторить действие!';
	const STATUS_WRONG_CONDITIONS = 'Соблюдены не все условия перехода в выбранный статус. Возможно, осмотр уже был изменён. Попробуйте обновить данные на своей стороне и повторить действие!';

	/**
	 * @SWG\Property()
	 * @var string
	 */
	public $success;

	/**
	 * @SWG\Property()
	 * @var string
	 */
	public $reason;

	/**
	 * @SWG\Property()
	 * @var string
	 */
	public $code;

	public function __construct($error, $code = null) {
		$this->success = false;
		$this->reason = $error;

		if ($code !== null) {
			$this->code = (string)$code;
		} else {
			$reflect = new ReflectionClass(get_class($this));
			$constants = array_flip($reflect->getConstants());
			$this->code = isset($constants[(string)$error]) ? $constants[(string)$error] : '';
		}
	}//  __construct()

	public static function getCodeByText($error) {
		$reflect = new ReflectionClass(self::class);
		$constants = array_flip($reflect->getConstants());
		return isset($constants[(string)$error]) ? $constants[(string)$error] : null;
	}

}// class ApiError
