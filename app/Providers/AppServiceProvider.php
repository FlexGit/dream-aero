<?php

namespace App\Providers;

use App\Models\Contractor;
use App\Services\HelpFunctions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Events\Dispatcher;

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
		error_reporting(E_ALL ^ E_NOTICE);
		
		Password::defaults(function () {
			return Password::min(8)
				->letters()
				->mixedCase()
				->numbers()
				->symbols()
				->uncompromised();
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
