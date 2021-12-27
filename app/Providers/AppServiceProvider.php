<?php

namespace App\Providers;

use App\Services\HelpFunctions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

use Validator;
use App\Models\City;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
		Password::defaults(function () {
			return Password::min(8)
				->letters()
				->mixedCase()
				->numbers()
				->symbols()
				->uncompromised();
		});
	
		$events->listen(BuildingMenu::class, function (BuildingMenu $event) {
			$orderCount = HelpFunctions::getNewOrderCount();
			$event->menu->addAfter('calendar', [
				'key'		  => 'order',
				'text'        => 'Заявки',
				'url'         => '/order',
				'icon'        => 'fas fa-chalkboard',
				'label'       => $orderCount ?: '',
				'label_color' => $orderCount ? 'success' : '',
			]);
			
			$dealCount = HelpFunctions::getNewDealCount();
			$event->menu->addAfter('order', [
				'key'		  => 'deal',
				'text'        => 'Сделки',
				'url'         => '/deal',
				'icon'        => 'fas fa-handshake',
				'label'       => $dealCount ?: '',
				'label_color' => $dealCount ? 'success' : '',
			]);
			
			$billCount = HelpFunctions::getNewBillCount();
			$event->menu->addAfter('certificate', [
				'key'		  => 'bill',
				'text'        => 'Счета',
				'url'         => '/bill',
				'icon'        => 'fas fa-fw fa-file-invoice-dollar',
				'label'       => $billCount ?: '',
				'label_color' => $billCount ? 'success' : '',
			]);
			
			$paymentCount = HelpFunctions::getNewPaymentCount();
			$event->menu->addAfter('bill', [
				'key'		  => 'payment',
				'text'        => 'Платежи',
				'url'         => '/payment',
				'icon'        => 'fas fa-fw fa-coins',
				'label'       => $paymentCount ?: '',
				'label_color' => $paymentCount ? 'success' : '',
			]);
		});
		
		Validator::extend('valid_city', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
			
			$city = City::find($inputs['city_id']);
			if (!$city || !$city->is_active) {
				return false;
			}
			
			return true;
		});
	
		Validator::extend('valid_phone', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
		
			if (!preg_match('/(\+7)[0-9]{10}/', $inputs['phone'])) {
				return false;
			}
		
			return true;
		});
    }
}
