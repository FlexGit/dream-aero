<?php

namespace App\Providers;

use App\Models\Contractor;
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
			/*$orderCount = HelpFunctions::getNewOrderCount();*/
			/*$event->menu->addAfter('calendar', [
				'key'		  => 'order',
				'text'        => 'Заявки',
				'url'         => '/order',
				'icon'        => 'fas fa-chalkboard',
				'label'       => $orderCount ?: '',
				'label_color' => $orderCount ? 'success' : '',
			]);*/
			
			/*$dealCount = HelpFunctions::getNewDealCount();*/
			/*$event->menu->addAfter('calendar', [
				'key'		  => 'deal',
				'text'        => 'Сделки',
				'url'         => '/deal',
				'icon'        => 'fas fa-handshake',
				'label'       => $dealCount ?: '',
				'label_color' => $dealCount ? 'success' : '',
			]);*/
			
			/*$billCount = HelpFunctions::getNewBillCount();*/
			/*$event->menu->addAfter('deal', [
				'key'		  => 'bill',
				'text'        => 'Счета',
				'url'         => '/bill',
				'icon'        => 'fas fa-fw fa-coins',
				'label'       => $billCount ?: '',
				'label_color' => $billCount ? 'success' : '',
			]);*/
			
			/*$paymentCount = HelpFunctions::getNewPaymentCount();*/
			/*$event->menu->addAfter('bill', [
				'key'		  => 'payment',
				'text'        => 'Платежи',
				'url'         => '/payment',
				'icon'        => 'fas fa-fw fa-coins',
				'label'       => $paymentCount ?: '',
				'label_color' => $paymentCount ? 'success' : '',
			]);*/
		});
		
		Validator::extend('valid_city', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();

			$city = City::find($inputs['city_id']);
			if (!$city || !$city->is_active || (\Auth::check() && \Auth::user()->city && \Auth::user()->city->id != $city->id)) {
				return false;
			}
			
			return true;
		});
	
		Validator::extend('valid_password', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
			
			if (!HelpFunctions::isValidMd5($inputs['password'])) {
				return false;
			}
		
			return true;
		});
	
		Validator::extend('valid_password_confirmation', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
		
			if (!HelpFunctions::isValidMd5($inputs['password_confirmation'])) {
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
	
		Validator::extend('unique_email', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
			
			$contractor = Contractor::where('email', $inputs['email']);
			if (isset($inputs['contractor_id'])) {
				$contractor = $contractor->where('id', '!=', $inputs['contractor_id']);
			} elseif (isset($inputs['id'])) {
				$contractor = $contractor->where('id', '!=', $inputs['id']);
			}
			$contractor = $contractor->first();
			if ($contractor) return false;

			return true;
		});
	
		Validator::extend('unique_phone', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
		
			$contractor = Contractor::where('phone', $inputs['phone'])
				->first();
			if ($contractor) return false;
		
			return true;
		});
    }
}
