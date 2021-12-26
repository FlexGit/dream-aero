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
			$table->string('version', 25)->nullable()->comment('версия сайта');
			$table->string('timezone', 50)->nullable()->comment('временная зона');
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
			],
			'spb' => [
				'name' => 'Санкт-Петербург',
				'sort' => 20,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
			],
			'vrn' => [
				'name' => 'Воронеж',
				'sort' => 30,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
			],
			'kzn' => [
				'name' => 'Казань',
				'sort' => 40,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
			],
			'krd' => [
				'name' => 'Краснодар',
				'sort' => 50,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
			],
			'nnv' => [
				'name' => 'Нижний Новгород',
				'sort' => 60,
				'version' => 'ru',
				'timezone' => 'Europe/Moscow',
			],
			'sam' => [
				'name' => 'Самара',
				'sort' => 70,
				'version' => 'ru',
				'timezone' => 'Europe/Samara',
			],
			'ekb' => [
				'name' => 'Екатеринбург',
				'sort' => 80,
				'version' => 'ru',
				'timezone' => 'Asia/Yekaterinburg',
			],
			'nsk' => [
				'name' => 'Новосибирск',
				'sort' => 90,
				'version' => 'ru',
				'timezone' => 'Asia/Novosibirsk',
			],
			'khv' => [
				'name' => 'Хабаровск',
				'sort' => 100,
				'version' => 'ru',
				'timezone' => 'Asia/Vladivostok',
			],
			'uae' => [
				'name' => 'Dubai',
				'sort' => 10,
				'version' => 'en',
				'timezone' => 'Asia/Dubai',
			],
			'dc' => [
				'name' => 'Washington D.C.',
				'sort' => 20,
				'version' => 'en',
				'timezone' => 'America/New_York',
			],
		];
	
		foreach ($cities as $alias => $item) {
			$city = new City();
			$city->alias = $alias;
			$city->name = $item['name'];
			$city->sort = $item['sort'];
			$city->version = $item['version'];
			$city->timezone = $item['timezone'];
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
