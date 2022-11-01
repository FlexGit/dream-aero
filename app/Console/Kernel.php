<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\LoadPlatformData::class,
		Commands\SendCertificateEmail::class,
		Commands\SendFlightInvitationEmail::class,
		Commands\AddContractorScore::class,
		Commands\SendPromocodeAfterFlightEmail::class,
		Commands\Roistat\RoistatAddDeals::class,
		Commands\RunAeroflotAccrual::class,
		Commands\UnlockPeriod::class,
		Commands\SendLeaveReviewEmail::class,
		Commands\SendPromocodeAfterYearEmail::class,
		Commands\YandexMarket::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// запуск демона для обработки задач из очереди
		$filePath = storage_path('logs/queue_worker.log');
		$schedule->command('queue:work --daemon')
			->everyMinute()
			/*->withoutOverlapping()*/
			->runInBackground()
			->appendOutputTo($filePath);
		
		// перезапуск демона очереди (чтобы изменения в коде были применены)
		$schedule->command('queue:restart')
			->everyFiveMinutes()
			->runInBackground()
			->appendOutputTo($filePath);
		
		// загрузка данных платформы из письма
		$filePath = storage_path('logs/commands/platform_data.log');
		$schedule->command('platform_data:load')
			->everyFiveMinutes()
			->runInBackground()
			->appendOutputTo($filePath);

		// отправка контрагенту сертификата на полет
		/*$filePath = storage_path('logs/commands/certificate_email.log');
		$schedule->command('certificate_email:send')
			->everyMinute()
			->runInBackground()
			->appendOutputTo($filePath)
			->emailOutputOnFailure(env('DEV_EMAIL'));*/
		
		// отправка контрагенту приглашения на полет
		$filePath = storage_path('logs/commands/flight_invitation_email.log');
		$schedule->command('flight_invitation_email:send')
			->everyFiveMinutes()
			->runInBackground()
			->appendOutputTo($filePath);
		
		// отправка контрагенту промокода на полет на другом типе тренажера
		$filePath = storage_path('logs/commands/promocode_send.log');
		$schedule->command('promocode_email:send')
			->hourly()
			->runInBackground()
			->appendOutputTo($filePath);
		
		// отправка контрагенту промокода на полет на другом типе тренажера
		/*$filePath = storage_path('logs/commands/promocode_year.log');
		$schedule->command('promocode_year:send')
			->everyThirtyMinutes()
			->runInBackground()
			->appendOutputTo($filePath);*/
		
		// отправка контрагенту промо по ДР
		$filePath = storage_path('logs/commands/birthday_promo.log');
		$schedule->command('birthday_promo:send')
			->daily()
			->runInBackground()
			->appendOutputTo($filePath);

		// начисление баллов после полета
		$filePath = storage_path('logs/commands/scoring.log');
		$schedule->command('score:add')
			->hourly()
			->runInBackground()
			->appendOutputTo($filePath);
		
		// проставление пилота после полета
		/*$filePath = storage_path('logs/commands/pilot_set.log');
		$schedule->command('pilot:set')
			->everyFiveMinutes()
			->runInBackground()
			->appendOutputTo($filePath);*/
		
		// получение информации о списании баллов Аэрофлот Бонус
		/*$filePath = storage_path('logs/commands/aeroflot_order_info.log');
		$schedule->command('aeroflot_order_info:get')
			->everyMinute()
			->runInBackground()
			->appendOutputTo($filePath);*/

		// Загрузка Сделок в Roistat
		$filePath = storage_path('logs/commands/roistat.log');
		$schedule->command('roistat:add_deals')
			->hourly()
			->runInBackground()
			->appendOutputTo($filePath);
		
		// Начисление миль Аэрофлот Бонус
		$filePath = storage_path('logs/commands/aeroflot_accrual.log');
		$schedule->command('aeroflot_accrual:run')
			->hourly()
			->runInBackground()
			->appendOutputTo($filePath);
		
		// Начисление миль Аэрофлот Бонус
		$schedule->command('period:unlock')
			->everyMinute()
			->runInBackground();
		
		// отправка контрагенту письма с предложением оставить отзыв
		$schedule->command('leave_review_email:send')
			->hourly()
			->runInBackground();
		
		// Обновление остатков на Яндекс Маркет
		$schedule->command('yandex:market')
			->everyThirtyMinutes()
			->runInBackground();
	}

	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__.'/Commands');

		require base_path('routes/console.php');
	}
}
