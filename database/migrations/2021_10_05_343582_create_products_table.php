<?php

use App\Models\City;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование продукта');
			$table->string('alias')->comment('алиас');
			$table->integer('product_type_id')->default(0)->index()->comment('тип продукта');
			$table->integer('employee_id')->default(0)->index()->comment('пилот');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором действует продукт');
			$table->integer('duration')->comment('длительность полёта, мин.');
			$table->integer('price')->default(0)->comment('базовая цена продукта');
			$table->boolean('is_hit')->default(0)->comment('является ли продукт хитом продаж');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->boolean('is_unified')->default(true)->index()->comment('сертификат действует на всех локациях');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
            $table->timestamps();
			$table->softDeletes();
        });
	
        $regular = HelpFunctions::getProductTypeByAlias(ProductType::REGULAR_ALIAS);
		$ultimate = HelpFunctions::getProductTypeByAlias(ProductType::ULTIMATE_ALIAS);
		$courses = HelpFunctions::getProductTypeByAlias(ProductType::COURSES_ALIAS);
		$vip = HelpFunctions::getProductTypeByAlias(ProductType::VIP_ALIAS);
		$services = HelpFunctions::getProductTypeByAlias(ProductType::SERVICES_ALIAS);
	
		$city = HelpFunctions::getCityByAlias(City::MSK_ALIAS);
		
		$items = [
			'0' => [
				'name' => 'Regular 30',
				'alias' => 'regular_30',
				'product_type_id' => $regular ? $regular->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 30,
				'price' => 6300,
				'is_hit' => true,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'1' => [
				'name' => 'Regular 60',
				'alias' => 'regular_60',
				'product_type_id' => $regular ? $regular->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 60,
				'price' => 10900,
				'is_hit' => true,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'2' => [
				'name' => 'Regular 90',
				'alias' => 'regular_90',
				'product_type_id' => $regular ? $regular->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 90,
				'price' => 15900,
				'is_hit' => false,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'3' => [
				'name' => 'Regular 120',
				'alias' => 'regular_120',
				'product_type_id' => $regular ? $regular->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 120,
				'price' => 20500,
				'is_hit' => false,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'4' => [
				'name' => 'Regular 180',
				'alias' => 'regular_180',
				'product_type_id' => $regular ? $regular->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 180,
				'price' => 26000,
				'is_hit' => false,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'5' => [
				'name' => 'Ultimate 30',
				'alias' => 'ultimate_30',
				'product_type_id' => $ultimate ? $ultimate->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 30,
				'price' => 7500,
				'is_hit' => true,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'6' => [
				'name' => 'Ultimate 60',
				'alias' => 'ultimate_60',
				'product_type_id' => $ultimate ? $ultimate->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 60,
				'price' => 12900,
				'is_hit' => true,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'7' => [
				'name' => 'Ultimate 90',
				'alias' => 'ultimate_90',
				'product_type_id' => $ultimate ? $ultimate->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 90,
				'price' => 18800,
				'is_hit' => false,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'8' => [
				'name' => 'Ultimate 120',
				'alias' => 'ultimate_120',
				'product_type_id' => $ultimate ? $ultimate->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 120,
				'price' => 24200,
				'is_hit' => false,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'9' => [
				'name' => 'Ultimate 180',
				'alias' => 'ultimate_180',
				'product_type_id' => $ultimate ? $ultimate->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 180,
				'price' => 34500,
				'is_hit' => false,
				'is_unified' => true,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'10' => [
				'name' => 'Денис Окань',
				'alias' => 'denis_okan',
				'product_type_id' => $vip ? $vip->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 150,
				'price' => 20000,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => false,
					'is_certificate_allow' => true,
				],
			],
			'11' => [
				'name' => 'Летчик Леха',
				'alias' => 'letchik_leha',
				'product_type_id' => $vip ? $vip->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 150,
				'price' => 20000,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => false,
					'is_certificate_allow' => true,
				],
			],
			'12' => [
				'name' => 'Platinum 150',
				'alias' => 'platinum_150',
				'product_type_id' => $courses ? $courses->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 150,
				'price' => 28900,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => false,
					'is_certificate_allow' => true,
				],
			],
			'13' => [
				'name' => 'Курс пилота (Basic)',
				'alias' => 'basic',
				'product_type_id' => $courses ? $courses->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 360,
				'price' => 49000,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => false,
					'is_certificate_allow' => true,
				],
			],
			'14' => [
				'name' => 'Курс пилота (Advanced)',
				'alias' => 'advanced',
				'product_type_id' => $courses ? $courses->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 360,
				'price' => 49000,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => false,
					'is_certificate_allow' => true,
				],
			],
			'15' => [
				'name' => 'Курс пилота (Expert)',
				'alias' => 'expert',
				'product_type_id' => $courses ? $courses->id : 0,
				'city_id' => $city ? $city->id : 0,
				'duration' => 540,
				'price' => 67500,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => false,
					'is_certificate_allow' => true,
				],
			],
			'16' => [
				'name' => 'Летаем без страха',
				'alias' => 'no_fear_fly',
				'product_type_id' => $courses ? $courses->id : 0,
				'city_id' => 0,
				'duration' => 0,
				'price' => 9900,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'17' => [
				'name' => 'Видеозапись',
				'alias' => 'video',
				'product_type_id' => $services ? $services->id : 0,
				'city_id' => 0,
				'duration' => 0,
				'price' => 500,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'18' => [
				'name' => 'Фотосъемка 20',
				'alias' => 'photo_20',
				'product_type_id' => $services ? $services->id : 0,
				'city_id' => 0,
				'duration' => 0,
				'price' => 1000,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
			'19' => [
				'name' => 'Фотосъемка 30',
				'alias' => 'photo_30',
				'product_type_id' => $services ? $services->id : 0,
				'city_id' => 0,
				'duration' => 0,
				'price' => 2000,
				'is_hit' => false,
				'is_unified' => false,
				'data' => [
					'is_order_allow' => true,
					'is_certificate_allow' => true,
				],
			],
		];
	
		foreach ($items as $item) {
			$product = new Product();
			$product->name = $item['name'];
			$product->alias = $item['alias'];
			$product->product_type_id = $item['product_type_id'];
			$product->employee_id = 0;
			$product->city_id = $item['city_id'];
			$product->duration = $item['duration'];
			$product->price = $item['price'];
			$product->is_hit = (bool)$item['is_hit'];
			$product->is_unified = (bool)$item['is_unified'];
			$product->data_json = $item['data'];
			$product->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
