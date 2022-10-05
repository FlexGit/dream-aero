<?php

namespace App\Console\Commands;

use App\Http\Controllers\ContractorController;
use App\Models\Bill;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Deal;
use App\Models\Discount;
use App\Models\Notification;
use App\Models\ProductType;
use App\Models\Promocode;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Throwable;
use Mail;

class SendPromocodeAfterYearEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promocode_year:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an E-mail to contractor with promocode after a year after purchase or bookings';

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
		$discount = HelpFunctions::getEntityByAlias(Discount::class, 'percent_10');
		if (!$discount) return 0;
	
		DB::connection()->enableQueryLog();
    	// проверяем все полеты с начала дня до текущего момента
		$contractors = DB::table('contractors')
			->selectRaw('DISTINCT contractors.id')
			->join('deals', 'deals.contractor_id', '=', 'contractors.id')
			->join('statuses as deal_statuses', function ($join) {
				$join->on('deals.status_id', '=', 'deal_statuses.id')
					->whereNotIn('deal_statuses.alias', [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS]);
			})
			->whereBetween('deals.created_at', [Carbon::now()->subDays(364)->startOfDay()->format('Y-m-d H:i:s'), Carbon::now()->subDays(364)->endOfDay()->format('Y-m-d H:i:s')])
			->where('contractors.email', '!=', Contractor::ANONYM_EMAIL)
			->where('contractors.is_active', true)
			->where('contractors.email', env('DEV_EMAIL'))
			->get();
    	foreach ($contractors as $contractor) {
    		$promocode = Promocode::where('type', Promocode::YEAR_TYPE)
				->where('contractor_id', $contractor->id)
				->first();
    		if ($promocode) continue;
    		
    		$dealCount = Deal::where('contractor_id', $contractor->id)
				->whereBetween('created_at', [Carbon::now()->format('Y-m-d H:i:s'), Carbon::now()->subDays(364)->endOfDay()->format('Y-m-d H:i:s')])
				->whereHas('status', function ($query) {
					$query->whereNotIn('statuses.alias', [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS]);
				})->count();
    		
    		// если за год были еще сделки, то игнорируем
    		if ($dealCount) continue;
		
			$contractor = Contractor::find($contractor->id);
			if (!$contractor) continue;
			
    		$city = $contractor->city;
    		
			try {
				DB::beginTransaction();
				
				$promocode = new Promocode();
				$promocode->number = $contractor->id . rand(10000, 99999);
				$promocode->type = Promocode::YEAR_TYPE;
				$promocode->contractor_id = $contractor->id;
				$promocode->discount_id = $discount->id;
				$promocode->active_from_at = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
				$promocode->active_to_at = Carbon::now()->addDays(30)->startOfDay()->format('Y-m-d H:i:s');
				$promocode->save();
				
				// отправим в мобилку уведомление о промокоде тоже
				$notification = new Notification();
				$notification->title = 'Дарим скидку ' . ($promocode->discount->valueFormatted() ?? '') . ' по персональному промокоду';
				$notification->description = 'Только до ' . Carbon::parse($promocode->active_to_at)->format('d.m.Y') . ' у Вас есть уникальная возможность приобрести сертификат или забронировать полёт со скидкой ' . ($promocode->discount->valueFormatted() ?? '') . '. Просто введите промо-код ' . ($promocode->number ?? '') . ' на нашем сайте или в приложении.';
				$notification->city_id = $city ? $city->id : 0;
				$notification->contractor_id = $contractor->id;
				$notification->is_active = true;
				$notification->save();
			
				$notification->contractors()->attach($contractor->id);
				
				$recipients = $bcc = [];
				$recipients[] = $contractor->email;
				$bcc[] = env('DEV_EMAIL');
				
				$messageData = [
					'contractor' => $contractor,
					'promocode' => $promocode,
					'city' => $city ?: new City(),
				];
				
				$subject = env('APP_NAME') . ': скидка на полет в Авиатренажере до ' . Carbon::parse($promocode->active_to_at)->format('d.m.Y');
				
				Mail::send(['html' => "admin.emails.year_promocode"], $messageData, function ($message) use ($subject, $recipients, $bcc) {
					/** @var \Illuminate\Mail\Message $message */
					$message->subject($subject);
					$message->to($recipients);
					$message->bcc($bcc);
				});
				$failures = Mail::failures();
				if ($failures) {
					DB::rollback();
				}

				$promocode->sent_at = Carbon::now()->format('Y-m-d H:i:s');
				$promocode->save();
				
				DB::commit();
			} catch (Throwable $e) {
				\Log::debug('500 - ' . $e->getMessage());
				
				return 0;
			}
		}
		\Log::debug(\DB::getQueryLog());
		
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - promocode_year:send - OK');
    	
        return 0;
    }
}
