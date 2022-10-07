<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Contractor;
use App\Models\Notification;
use App\Models\Promo;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Throwable;
use Mail;

class SendBirthdayPromoEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday_promo:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor with notification that he can have a Birthday discount';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$promo = HelpFunctions::getEntityByAlias(Promo::class, Promo::BIRTHDAY_ALIAS);
		if (!$promo) return 0;

		DB::connection()->enableQueryLog();
		$contractors = Contractor::whereMonth('birthdate', '=', Carbon::now()->format('m'))
			->whereDay('birthdate', '=', Carbon::now()->addDays(3)->format('d'))
			->where('contractors.email', '!=', Contractor::ANONYM_EMAIL)
			->where('contractors.is_active', true)
			/*->where('contractors.email', env('DEV_EMAIL'))*/
			->get();
    	foreach ($contractors as $contractor) {
    		$city = $contractor->city;
    		
			try {
				DB::beginTransaction();
				
				// отправим в мобилку уведомление
				$notification = new Notification();
				$notification->title = 'Дарим скидку ' . ($promo->discount->valueFormatted() ?? '') . ' по акции в честь Вашего Дня Рождения';
				$notification->description = 'Поздравляем с наступающим Днем Рождения! Спешим напомнить, что с сегодняшнего дня и до ' . Carbon::now()->addDays(6)->format('d.m.Y') . ' у вас есть возможность забронировать полет со скидкой ' . ($promo->discount->valueFormatted() ?? '') . ' в честь праздника. На подарочные сертификаты с открытой датой акция не распространяется.';
				$notification->city_id = $city ? $city->id : 0;
				$notification->contractor_id = $contractor->id;
				$notification->is_active = true;
				$notification->save();
			
				$notification->contractors()->attach($contractor->id);
				
				$recipients = $bcc = [];
				$recipients[] = $contractor->email;
				//$bcc[] = env('DEV_EMAIL');
				
				$messageData = [
					'contractor' => $contractor,
					'promo' => $promo,
					'city' => $city ?: new City(),
				];
				
				$subject = env('APP_NAME') . ': скидка на полет в Авиатренажере по акции в честь Дня Рождения';
				
				Mail::send(['html' => "admin.emails.birthday_promo"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
					/** @var \Illuminate\Mail\Message $message */
					$message->subject($subject);
					$message->to($recipients);
					$message->bcc($bcc);
				});
				$failures = Mail::failures();
				if ($failures) {
					DB::rollback();
				}

				DB::commit();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
				
				return 0;
			}
		}
		\Log::debug(\DB::getQueryLog());
		
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - birthday_promo:send - OK');
    	
        return 0;
    }
}
