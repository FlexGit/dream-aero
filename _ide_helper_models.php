<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Bill
 *
 * @property int $id
 * @property string|null $number номер счета
 * @property int $status_id статус
 * @property int $amount сумма счета
 * @property int $deal_id сделка, по которой выставлен счет
 * @property int $deal_position_id позиция сделки, по которой выставлен счет
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property int $user_id пользователь
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Deal|null $deal
 * @property-read \App\Models\DealPosition|null $dealPosition
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newQuery()
 * @method static \Illuminate\Database\Query\Builder|Bill onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Bill withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Bill withoutTrashed()
 * @mixin \Eloquent
 */
	class Bill extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Certificate
 *
 * @property int $id
 * @property string $number номер сертификата
 * @property int $status_id статус
 * @property int $contractor_id контрагент
 * @property int $product_id продукт
 * @property int $city_id город
 * @property \datetime|null $expire_at срок окончания действия сертификата
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Query\Builder|Certificate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Certificate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Certificate withoutTrashed()
 * @mixin \Eloquent
 * @property bool $is_unified сертификат действует во всех городах
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereIsUnified($value)
 */
	class Certificate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\City
 *
 * @property int $id
 * @property string $name наименование
 * @property string $alias алиас
 * @property string|null $version версия сайта
 * @property string|null $timezone временная зона
 * @property int $sort сортировка
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация: часовой пояс
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $location
 * @property-read int|null $location_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Query\Builder|City onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|City withTrashed()
 * @method static \Illuminate\Database\Query\Builder|City withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Code
 *
 * @property int $id
 * @property string $code код подтверждения
 * @property string $email E-mail
 * @property int $contractor_id Контрагент
 * @property bool $is_reset признак использования
 * @property \datetime|null $reset_at дата использования кода подтверждения
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Code newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Code newQuery()
 * @method static \Illuminate\Database\Query\Builder|Code onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Code query()
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereIsReset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereResetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Code withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Code withoutTrashed()
 * @mixin \Eloquent
 */
	class Code extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contractor
 *
 * @property int $id
 * @property string $name имя
 * @property string|null $lastname фамилия
 * @property \datetime|null $birthdate дата рождения
 * @property string|null $phone основной номер телефона
 * @property string $email основной e-mail
 * @property string|null $password пароль в md5
 * @property string|null $remember_token
 * @property int $city_id город, к которому привязан контрагент
 * @property \App\Models\Discount|null $discount скидка
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $last_auth_at дата последней по времени авторизации
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newQuery()
 * @method static \Illuminate\Database\Query\Builder|Contractor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastAuthAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Contractor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Contractor withoutTrashed()
 * @mixin \Eloquent
 */
	class Contractor extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Deal
 *
 * @property int $id
 * @property string|null $number номер сделки
 * @property int $contractor_id контрагент, с которым заключена сделка
 * @property array|null $data_json дополнительная информация
 * @property int $user_id пользователь
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DealPosition[] $dealPositions
 * @property-read int|null $deal_positions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal newQuery()
 * @method static \Illuminate\Database\Query\Builder|Deal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deal whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Deal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Deal withoutTrashed()
 * @mixin \Eloquent
 */
	class Deal extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DealPosition
 *
 * @property int $id
 * @property string $number номер позиции сделки
 * @property int $deal_id сделка
 * @property int $status_id статус
 * позиции сделки
 * @property int $order_id заказ
 * @property int $product_id продукт
 * @property int $certificate_id сертификат
 * @property int $duration продолжительность полета
 * @property int $amount стоимость
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property \datetime|null $flight_at дата и время полета
 * @property \datetime|null $invite_sent_at последняя дата отправки приглашения на e-mail
 * @property \datetime|null $certificate_sent_at последняя дата отправки сертификата на e-mail
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Deal|null $deal
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition newQuery()
 * @method static \Illuminate\Database\Query\Builder|DealPosition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCertificateSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereInviteSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealPosition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DealPosition withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DealPosition withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read int|null $bills_count
 */
	class DealPosition extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Discount
 *
 * @property int $id
 * @property string $value размер скидки
 * @property bool $is_fixed фиксированная скидка
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newQuery()
 * @method static \Illuminate\Database\Query\Builder|Discount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereIsFixed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|Discount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Discount withoutTrashed()
 * @mixin \Eloquent
 */
	class Discount extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string $name имя сотрудника
 * @property int $employee_position_id должность сотрудника
 * @property int $location_id локация сотрудника
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\EmployeePosition|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Query\Builder|Employee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmployeePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Employee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Employee withoutTrashed()
 * @mixin \Eloquent
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EmployeePosition
 *
 * @property int $id
 * @property string $name имя
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition newQuery()
 * @method static \Illuminate\Database\Query\Builder|EmployeePosition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EmployeePosition withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EmployeePosition withoutTrashed()
 * @mixin \Eloquent
 */
	class EmployeePosition extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FlightSimulator
 *
 * @property int $id
 * @property string $name наименование авиатренажера
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newQuery()
 * @method static \Illuminate\Database\Query\Builder|FlightSimulator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator query()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FlightSimulator withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FlightSimulator withoutTrashed()
 * @mixin \Eloquent
 */
	class FlightSimulator extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LegalEntity
 *
 * @property int $id
 * @property string $name наименование юр.лица
 * @property string $alias алиас
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity newQuery()
 * @method static \Illuminate\Database\Query\Builder|LegalEntity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LegalEntity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LegalEntity withoutTrashed()
 * @mixin \Eloquent
 */
	class LegalEntity extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $name наименование локации
 * @property string $alias alias
 * @property int $legal_entity_id юр.лицо, на которое оформлена локация
 * @property int $city_id город, в котором находится локация
 * @property int $sort сортировка
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employee[] $employee
 * @property-read int|null $employee_count
 * @property-read \App\Models\LegalEntity $legalEntity
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FlightSimulator[] $simulator
 * @property-read int|null $simulator_count
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Query\Builder|Location onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLegalEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Location withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Location withoutTrashed()
 * @mixin \Eloquent
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $title заголовок
 * @property string $description описание
 * @property int $city_id город
 * @property int $contractor_id контрагент
 * @property bool $is_new новое уведомление
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Deal|null $deal
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Query\Builder|Notification onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Notification withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Notification withoutTrashed()
 * @mixin \Eloquent
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property string|null $number номер заказа
 * @property int $status_id статус заказа
 * @property int $contractor_id контрагент, совершивший заказ
 * @property string $name имя
 * @property string $phone номер телефона
 * @property string $email e-mail
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property int $product_id продукт
 * @property int $amount стоимость
 * @property int $duration продолжительность полета
 * @property int $promocode_id промокод
 * @property int $certificate_id сертификат
 * @property \datetime|null $flight_at дата и время полета
 * @property bool $is_certificate_order заказ сертификата
 * @property bool $is_unified сертификат действует во всех городах
 * @property string|null $source источник
 * @property array|null $data_json дополнительная информация
 * @property int $user_id пользователь
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\DealPosition $dealPosition
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Promocode|null $promocode
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCertificateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFlightAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsCertificateOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsUnified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromocodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $payment_method_id способ платежа
 * @property int $status_id статус
 * @property int $amount сумма платежа
 * @property \datetime|null $performed_at дата проведения платежа шлюзом или ОФД
 * @property int $deal_id сделка, к которой привязан платеж
 * @property array|null $data_json дополнительная информация: ОФД - номер смены, состав позиций, номер ФД, №пп, оператор. Шлюз -
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Bill|null $bill
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Query\Builder|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePerformedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Payment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Payment withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $number номер платежа
 * @property int $bill_id счет, по которому совершен платеж
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNumber($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethod
 *
 * @property int $id
 * @property string $name наименование способа оплаты
 * @property string $alias алиас
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Query\Builder|PaymentMethod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PaymentMethod withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PaymentMethod withoutTrashed()
 * @mixin \Eloquent
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name наименование продукта
 * @property string $alias алиас
 * @property int $product_type_id тип продукта
 * @property int $employee_id пилот
 * @property int $city_id город, в котором действует продукт
 * @property int $duration длительность полёта, мин.
 * @property int $price базовая цена продукта
 * @property bool $is_hit является ли продукт хитом продаж
 * @property bool $is_active признак активности
 * @property bool $is_unified сертификат действует на всех локациях
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Employee|null $employee
 * @property-read \App\Models\ProductType|null $productType
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsUnified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductType
 *
 * @property int $id
 * @property string $name наименование типа продукта
 * @property string $alias алиас
 * @property bool $is_tariff является ли продукт тарифом
 * @property int $sort сортировка
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProductType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereIsTariff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProductType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductType withoutTrashed()
 * @mixin \Eloquent
 */
	class ProductType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Promo
 *
 * @property int $id
 * @property string $name наименование
 * @property int $discount_id скидка
 * @property string|null $preview_text анонс
 * @property string|null $detail_text описание
 * @property int $city_id город, к которому относится акция
 * @property bool $is_published для публикации
 * @property bool $is_active признак активности
 * @property \datetime|null $active_from_at дата начала активности
 * @property \datetime|null $active_to_at дата окончания активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Discount|null $discount
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Promo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo newQuery()
 * @method static \Illuminate\Database\Query\Builder|Promo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereActiveFromAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereActiveToAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDetailText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo wherePreviewText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Promo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Promo withoutTrashed()
 */
	class Promo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Promocode
 *
 * @property int $id
 * @property string $number промокод
 * @property int $city_id город, в котором действует промокод
 * @property \App\Models\Discount|null $discount_id скидка
 * @property bool $is_active признак активности
 * @property \datetime|null $active_from_at дата начала активности
 * @property \datetime|null $active_to_at дата окончания активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newQuery()
 * @method static \Illuminate\Database\Query\Builder|Promocode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveFromAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveToAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Promocode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Promocode withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\Discount|null $discount
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDiscountId($value)
 */
	class Promocode extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Review
 *
 * @property int $id
 * @property string $name имя пользователя
 * @property string|null $comment текст отзыва
 * @property int $location_id локация, о которой отзыв
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Query\Builder|Review onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Review withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Review withoutTrashed()
 * @mixin \Eloquent
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Score
 *
 * @property int $id
 * @property int $score количество баллов
 * @property int $contractor_id контрагент
 * @property int $deal_position_id ссылка на позицию сделки
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\Deal|null $deal
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Score newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Score newQuery()
 * @method static \Illuminate\Database\Query\Builder|Score onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Score query()
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Score whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Score withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Score withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\DealPosition|null $dealPosition
 */
	class Score extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Status
 *
 * @property int $id
 * @property string $name наименование
 * @property string $alias алиас
 * @property string $type тип сущности: контрагент, заказ, сделка, счет, платеж, сертификат
 * @property int $sort сортировка
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Query\Builder|Status onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Status withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Status withoutTrashed()
 * @mixin \Eloquent
 */
	class Status extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Token
 *
 * @property int $id
 * @property string $token токен
 * @property int $contractor_id контрагент
 * @property \datetime|null $expire_at Действует до
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Contractor $contractor
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Query\Builder|Token onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Token withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Token withoutTrashed()
 * @mixin \Eloquent
 */
	class Token extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property int $city_id город
 * @property int $location_id локация
 * @property bool $enable
 * @property string|null $remember_token
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

