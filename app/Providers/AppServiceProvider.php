<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
    public function boot()
    {
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
			if (!$city || !$city->is_active) {
				return false;
			}
			
			return true;
		});
	
		Validator::extend('valid_phone', function($attribute, $value, $parameters, $validator) {
			$inputs = $validator->getData();
		
			if (preg_match('/(+7)[0-9]{10}/', $inputs['phone'])) {
				return false;
			}
		
			return true;
		});
    }
}
