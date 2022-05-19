<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $event_type тип события
 * @property int $contractor_id Контрагент
 * @property int $deal_id сделка
 * @property int $deal_position_id позиция сделки
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property int $flight_simulator_id авиатренажер, на котором будет осуществлен полет
 * @property int $user_id
 * @property \datetime|null $start_at дата и время начала события
 * @property \datetime|null $stop_at дата и время окончания события
 * @property int $extra_time дополнительное время
 * @property int $is_repeated_flight признак повторного полета
 * @property int $is_unexpected_flight признак спонтанного полета
 * @property string|null $notification_type способ оповещения контрагента о полете
 * @property int $pilot_assessment оценка пилота
 * @property int $admin_assessment оценка админа
 * @property \datetime|null $simulator_up_at дата и время подъема платформы
 * @property \datetime|null $simulator_down_at дата и время опускания платформы
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventComment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\Deal|null $deal
 * @property-read \App\Models\DealPosition|null $dealPosition
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\FlightSimulator|null $simulator
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Query\Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAdminAssessment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereExtraTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsRepeatedFlight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsUnexpectedFlight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event wherePilotAssessment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSimulatorDownAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSimulatorUpAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStopAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Event withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Event withoutTrashed()
 * @mixin \Eloquent
 * @property bool $is_notified
 * @property \datetime|null $flight_invitation_sent_at
 * @property string|null $uuid
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFlightInvitationSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsNotified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUuid($value)
 */
class Event extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'event_type' => 'Тип события',
		'contractor_id' => 'Контрагент',
		'deal_id' => 'Сделка',
		'deal_position_id' => 'Позиция сделки',
		'city_id' => 'Город',
		'location_id' => 'Локация',
		'flight_simulator_id' => 'Авиатренажер',
		'user_id' => 'Пользователь',
		'start_at' => 'Начало события',
		'stop_at' => 'Окончание события',
		'extra_time' => 'Дополнительное время',
		'is_repeated_flight' => 'Повторный полет',
		'is_unexpected_flight' => 'Спонтанный полет',
		'notification_type' => 'Способ уведомления',
		'is_notified' => 'Контрагент уведомлен о полете',
		'pilot_assessment' => 'Оценка пилота',
		'admin_assessment' => 'Оценка администратора',
		'simulator_up_at' => 'Время подъема платформы',
		'simulator_down_at' => 'Время опускания платформы',
		'flight_invitation_sent_at' => 'Дата отправки приглашения на полет',
		'pilot_id' => 'Фактический пилот',
		'uuid' => 'Uuid',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	protected $dontKeepRevisionOf = ['uuid', 'data_json'];
	
	const EVENT_SOURCE_DEAL = 'deal';
	const EVENT_SOURCE_CALENDAR = 'calendar';

	const EVENT_TYPE_DEAL = 'deal';
	const EVENT_TYPE_DEAL_PAID = 'deal_paid';
	const EVENT_TYPE_SHIFT_ADMIN = 'shift_admin';
	const EVENT_TYPE_SHIFT_PILOT = 'shift_pilot';
	const EVENT_TYPE_BREAK = 'break';
	const EVENT_TYPE_CLEANING = 'cleaning';
	const EVENT_TYPE_TEST_FLIGHT = 'test_flight';
	const EVENT_TYPE_USER_FLIGHT = 'user_flight';
	const EVENT_TYPES = [
		self::EVENT_TYPE_DEAL => 'Сделка не оплачена',
		self::EVENT_TYPE_DEAL_PAID => 'Сделка оплачена',
		self::EVENT_TYPE_SHIFT_ADMIN => 'Смена администратора',
		self::EVENT_TYPE_SHIFT_PILOT => 'Смена пилота',
		self::EVENT_TYPE_BREAK => 'Перерыв',
		self::EVENT_TYPE_CLEANING => 'Уборка',
		self::EVENT_TYPE_TEST_FLIGHT => 'Тестовый',
		self::EVENT_TYPE_USER_FLIGHT => 'Сотрудник',
	];
	
	const NOTIFICATION_TYPE_SMS = 'sms';
	const NOTIFICATION_TYPE_CALL = 'call';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'event_type',
		'contractor_id',
		'deal_id',
		'deal_position_id',
		'user_id',
		'extra_time',
		'city_id',
		'location_id',
		'flight_simulator_id',
		'start_at',
		'stop_at',
		'is_repeated_flight',
		'is_unexpected_flight',
		'pilot_assessment',
		'admin_assessment',
		'simulator_up_at',
		'simulator_down_at',
		'notification_type',
		'is_notified',
		'flight_invitation_sent_at',
		'pilot_id',
		'test_pilot_id',
		'employee_id',
		'uuid',
		'data_json',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'start_at' => 'datetime:Y-m-d H:i:s',
		'stop_at' => 'datetime:Y-m-d H:i:s',
		'simulator_up_at' => 'datetime:Y-m-d H:i:s',
		'simulator_down_at' => 'datetime:Y-m-d H:i:s',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'flight_invitation_sent_at' => 'datetime:Y-m-d H:i',
		'data_json' => 'array',
		'is_notified' => 'boolean',
	];
	
	public static function boot()
	{
		parent::boot();
		
		Event::created(function (Event $event) {
			$event->uuid = $event->generateUuid();
			$event->user_id = \Auth::user()->id;
			$event->save();
		});
		
		Event::deleting(function (Event $event) {
			$event->comments()->delete();
		});
		
		Event::saved(function (Event $event) {
			$deal = $event->deal;
			if ($event->user && $deal) {
				$status = HelpFunctions::getEntityByAlias(Status::class, Deal::IN_WORK_STATUS);
				if ($status) {
					$deal->status_id = $status->id;
					$deal->save();
				}
			}
		});
	}
	
	public function contractor()
	{
		return $this->belongsTo(Contractor::class, 'contractor_id', 'id');
	}

	public function city()
	{
		return $this->belongsTo(City::class, 'city_id', 'id');
	}
	
	public function location()
	{
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

	public function simulator()
	{
		return $this->belongsTo(FlightSimulator::class, 'flight_simulator_id', 'id');
	}
	
	public function deal()
	{
		return $this->belongsTo(Deal::class, 'deal_id', 'id');
	}

	public function dealPosition()
	{
		return $this->belongsTo(DealPosition::class, 'deal_position_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	
	public function pilot()
	{
		return $this->belongsTo(User::class, 'pilot_id', 'id');
	}
	
	public function testPilot()
	{
		return $this->belongsTo(User::class, 'test_pilot_id', 'id');
	}
	
	public function employee()
	{
		return $this->belongsTo(User::class, 'employee_id', 'id');
	}
	
	public function comments()
	{
		return $this->hasMany(EventComment::class, 'event_id', 'id')
			->latest();
	}
	
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function generateUuid()
	{
		return (string)\Webpatser\Uuid\Uuid::generate();
	}
	
	/**
	 * @return $this|null
	 */
	public function generateFile()
	{
		$simulatorAlias = $this->simulator->alias ?? '';
		if (!$simulatorAlias) return null;

		$product = $this->dealPosition->product;
		if (!$product) return null;
		
		$productType = $product->productType;
		if (!$productType) return null;
		
		$flightInvitationTemplateFileName = 'INVITE_' . $simulatorAlias . '.jpg';
		if (!Storage::disk('private')->exists('invitation/template/' . $flightInvitationTemplateFileName)) {
			return null;
		}
		
		$flightInvitationTemplateFilePath = Storage::disk('private')->path('invitation/template/' . $flightInvitationTemplateFileName);
		
		$flightInvitationFile = Image::make($flightInvitationTemplateFilePath)->encode('jpg');
		$fontPath = public_path('assets/fonts/GothamProRegular/GothamProRegular.ttf');
		
		$flightInvitationFile->text($this->start_at->format('d.m.Y H:i'), 840, 100, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(24);
			$font->color('#000000');
		});
		$flightInvitationFile->text($product->duration ?? '-', 250, 890, function ($font) use ($fontPath) {
			$font->file($fontPath);
			$font->size(35);
			$font->color('#000000');
		});
		
		$flightInvitationFileName = $this->uuid . '.jpg';
		if (!$flightInvitationFile->save(storage_path('app/private/invitation/' . $flightInvitationFileName))) {
			return null;
		}
		
		$data = $this->data_json ?? [];
		$data['flight_invitation_file_path'] = 'invitation/' . $flightInvitationFileName;
		
		$this->data_json = $data;
		if (!$this->save()) {
			return null;
		}
		
		return $this;
	}
}
