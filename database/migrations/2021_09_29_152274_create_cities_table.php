<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('наименование');
			$table->string('alias', 50)->comment('алиас');
			$table->string('version', 25)->default('ru')->index()->comment('версия');
			$table->string('timezone', 50)->nullable()->comment('временная зона');
			$table->string('email')->nullable()->comment('e-mail');
			$table->string('phone')->nullable()->comment('телефон');
			$table->integer('sort')->default(0)->comment('сортировка');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация: часовой пояс');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$cities = [
			'msk' => [
				'name' => 'Москва',
				'sort' => 10,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
				'email' => 'msk@dream-aero.com',
				'phone' => '+7 (495) 532-87-37',
			],
			'spb' => [
				'name' => 'Санкт-Петербург',
				'sort' => 20,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
				'email' => 'info@dream-aero.com',
				'phone' => '+7 (812) 904-20-11',
			],
			'vrn' => [
				'name' => 'Воронеж',
				'sort' => 30,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
				'email' => 'vrn@dream-aero.com',
				'phone' => '+7 (920) 459-10-99',
			],
			'kzn' => [
				'name' => 'Казань',
				'sort' => 40,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
				'email' => 'kzn@dream-aero.com',
				'phone' => '+7 (843) 203-21-63',
			],
			'krd' => [
				'name' => 'Краснодар',
				'sort' => 50,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
				'email' => 'krd@dream-aero.com',
				'phone' => '+7 (861) 290-43-90',
			],
			'nnv' => [
				'name' => 'Нижний Новгород',
				'sort' => 60,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
				'email' => 'nnv@dream-aero.com',
				'phone' => '+7 (831) 283-42-20',
			],
			'sam' => [
				'name' => 'Самара',
				'sort' => 70,
				'version' => 'ru',
				'timezone' => 'Europe/Samara',
				'email' => 'sam@dream-aero.com',
				'phone' => '+7 (846) 225-02-45',
			],
			'ekb' => [
				'name' => 'Екатеринбург',
				'sort' => 80,
				'version' => 'ru',
				'timezone' => 'Asia/Yekaterinburg',
				'email' => 'ekb@dream-aero.com',
				'phone' => '+7 (343) 361-38-04',
			],
			'nsk' => [
				'name' => 'Новосибирск',
				'sort' => 90,
				'version' => 'ru',
				'timezone' => 'Asia/Novosibirsk',
				'email' => 'nsk@dream-aero.com',
				'phone' => '+7 (383) 375-23-10',
			],
			'khv' => [
				'name' => 'Хабаровск',
				'sort' => 100,
				'version' => 'ru',
				'timezone' => 'Asia/Vladivostok',
				'email' => 'khv@dream-aero.com',
				'phone' => '+7 (4212) 942-732',
			],
			'dc' => [
				'name' => 'Washington D.C.',
				'sort' => 10,
				'version' => 'en',
				'timezone' => 'America/New_York',
				'email' => 'dc@dream.aero',
				'phone' => '+1 240 224 48 85',
			],
		];
	
		foreach ($cities as $alias => $item) {
			$city = new City();
			$city->alias = $alias;
			$city->name = $item['name'];
			$city->sort = $item['sort'];
			$city->version = $item['version'];
			$city->timezone = $item['timezone'];
			$city->email = $item['email'];
			$city->phone = $item['phone'];
			$city->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
