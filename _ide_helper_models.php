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
 * App\Models\City
 *
 * @property int $id
 * @property string $name наименование города
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @mixin \Eloquent
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
 * @method static \Illuminate\Database\Eloquent\Builder|Code newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Code newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Code query()
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereIsReset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereResetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereContractorId($value)
 */
	class Code extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contractor
 *
 * @property int $id
 * @property string $name имя
 * @property string $phone основной номер телефона
 * @property string $email основной e-mail
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int $city_id город, к которому привязан контрагент
 * @property array $data_json дополнительная информация
 * @property \datetime|null $last_auth_at дата последней по времени авторизации
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereLastAuthAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Contractor extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string $name имя сотрудника
 * @property int $employee_position_id должность сотрудника
 * @property int $location_id локация сотрудника
 * @property array $data_json фото сотрудника
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\EmployeePosition|null $position
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmployeePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeePosition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class EmployeePosition extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\File
 *
 * @property int $id
 * @property string $path путь к файлу
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FlightSimulator
 *
 * @property int $id
 * @property string $name наименование авиатренажера
 * @property int $flight_simulator_type_id тип авиатренажера
 * @property int $location_id локация, в которой находится авиатренажер
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\FlightSimulatorType|null $simulatorType
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator query()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereFlightSimulatorTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereIsActive($value)
 */
	class FlightSimulator extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FlightSimulatorType
 *
 * @property int $id
 * @property string $name наименование типа авиатренажера
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FlightSimulator $simulator
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property bool $is_active признак активности
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereIsActive($value)
 */
	class FlightSimulatorType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LegalEntity
 *
 * @property int $id
 * @property string $name наименование юридического лица
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereUpdatedAt($value)
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
 * @property int $legal_entity_id юр.лицо, на которое оформлена локация
 * @property int $city_id город, в котором находится локация
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FlightSimulator[] $simulator
 * @property-read int|null $simulator_count
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\LegalEntity $legalEntity
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLegalEntityId($value)
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * App\MobAuth
 *
 * @property int $id
 * @property string $token токен
 * @property int $contractor_id контрагент
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Contractor $contractor
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereId($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MobAuth whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth query()
 * @method static \Illuminate\Database\Eloquent\Builder|MobAuth whereContractorId($value)
 */
	class MobAuth extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethod
 *
 * @property int $id
 * @property string $name наименование способа оплаты
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Review
 *
 * @property int $id
 * @property string $name имя пользователя
 * @property string $comment текст отзыва
 * @property int $location_id локация, о которой отзыв
 * @property int $is_active признак активности
 * @property \datetime|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tariff
 *
 * @property int $id
 * @property string $name наименование тарифа
 * @property int $tariff_type_id тип тарифа
 * @property int $city_id город, в котором действует продукт
 * @property int $duration длительность полёта, мин.
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property int $price базовая цена продукта
 * @property int $is_hit является ли продукт хитом продаж
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereIsHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereTariffTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Tariff extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TariffType
 *
 * @property int $id
 * @property string $name наименование тарифа
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType query()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TariffType extends \Eloquent {}
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
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $city_id город
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCityId($value)
 */
	class User extends \Eloquent {}
}

