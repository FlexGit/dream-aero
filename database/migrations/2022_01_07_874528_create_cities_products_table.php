<?php

use App\Models\City;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities_products', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('product_id')->nullable(false)->index();
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->unsignedBigInteger('city_id')->nullable(false)->index();
			$table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
			$table->integer('price')->default(0)->comment('базовая цена продукта');
			$table->integer('discount_id')->default(0)->index()->comment('скидка на продукт');
			$table->boolean('is_hit')->default(0)->comment('является ли продукт хитом продаж');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->timestamps();
        });
	
		$regular = HelpFunctions::getProductTypeByAlias(ProductType::REGULAR_ALIAS);
		$ultimate = HelpFunctions::getProductTypeByAlias(ProductType::ULTIMATE_ALIAS);
		$courses = HelpFunctions::getProductTypeByAlias(ProductType::COURSES_ALIAS);
		$vip = HelpFunctions::getProductTypeByAlias(ProductType::VIP_ALIAS);
		$services = HelpFunctions::getProductTypeByAlias(ProductType::SERVICES_ALIAS);
	
		$city = HelpFunctions::getCityByAlias(City::MSK_ALIAS);
	
		$items = [];

		$items[] = [
			'name' => 'Regular 30',
			'alias' => 'regular_30',
			'product_type_id' => $regular ? $regular->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 30,
			'price' => 6300,
			'is_hit' => true,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Regular 60',
			'alias' => 'regular_60',
			'product_type_id' => $regular ? $regular->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 60,
			'price' => 10900,
			'is_hit' => true,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Regular 90',
			'alias' => 'regular_90',
			'product_type_id' => $regular ? $regular->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 90,
			'price' => 15900,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Regular 120',
			'alias' => 'regular_120',
			'product_type_id' => $regular ? $regular->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 120,
			'price' => 20500,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Regular 180',
			'alias' => 'regular_180',
			'product_type_id' => $regular ? $regular->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 180,
			'price' => 26000,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Ultimate 30',
			'alias' => 'ultimate_30',
			'product_type_id' => $ultimate ? $ultimate->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 30,
			'price' => 7500,
			'is_hit' => true,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Ultimate 60',
			'alias' => 'ultimate_60',
			'product_type_id' => $ultimate ? $ultimate->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 60,
			'price' => 12900,
			'is_hit' => true,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Ultimate 90',
			'alias' => 'ultimate_90',
			'product_type_id' => $ultimate ? $ultimate->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 90,
			'price' => 18800,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Ultimate 120',
			'alias' => 'ultimate_120',
			'product_type_id' => $regular ? $regular->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 120,
			'price' => 24200,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Ultimate 180',
			'alias' => 'ultimate_180',
			'product_type_id' => $ultimate ? $ultimate->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 180,
			'price' => 34500,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 6,
			],
		];
		$items[] = [
			'name' => 'Platinum 150',
			'alias' => 'platinum_150',
			'product_type_id' => $courses ? $courses->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 150,
			'price' => 28900,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 12,
			],
		];
		$items[] = [
			'name' => 'Basic',
			'alias' => 'basic',
			'product_type_id' => $courses ? $courses->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 360,
			'price' => 49000,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 12,
			],
		];
		$items[] = [
			'name' => 'Advanced',
			'alias' => 'advanced',
			'product_type_id' => $courses ? $courses->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 360,
			'price' => 49000,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 12,
			],
		];
		$items[] = [
			'name' => 'Expert',
			'alias' => 'expert',
			'product_type_id' => $courses ? $courses->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 540,
			'price' => 67500,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 0,
			],
		];
		$items[] = [
			'name' => 'Видео-курс "Летаем без страха"',
			'alias' => 'fly_no_fear',
			'product_type_id' => $courses ? $courses->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 1200,
			'price' => 9900,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => true,
				'is_certificate_purchase_allow' => true,
				'validity' => 0,
			],
		];
		$items[] = [
			'name' => 'Денис Окань',
			'alias' => 'okan',
			'product_type_id' => $vip ? $vip->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 60,
			'price' => 20000,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => false,
				'is_certificate_purchase_allow' => true,
				'validity' => 12,
			],
		];
		$items[] = [
			'name' => 'Лётчик Лёха',
			'alias' => 'lekha',
			'product_type_id' => $vip ? $vip->id : 0,
			'city_id' => $city ? $city->id : 0,
			'duration' => 60,
			'price' => 20000,
			'is_hit' => false,
			'data' => [
				'is_booking_allow' => false,
				'is_certificate_purchase_allow' => true,
				'validity' => 12,
			],
		];
		$items[] = [
			'name' => 'Видеозапись',
			'alias' => 'video',
			'product_type_id' => $services ? $services->id : 0,
			'city_id' => 0,
			'duration' => 0,
			'price' => 500,
			'is_hit' => false,
			'data' => [
			],
		];
		$items[] = [
			'name' => 'Фотосъемка',
			'alias' => 'photo',
			'product_type_id' => $services ? $services->id : 0,
			'city_id' => 0,
			'duration' => 0,
			'price' => 500,
			'is_hit' => false,
			'data' => [
			],
		];
	
		foreach ($items as $item) {
			$product = new Product();
			$product->name = $item['name'];
			$product->alias = $item['alias'];
			$product->product_type_id = $item['product_type_id'];
			$product->duration = $item['duration'];
			$product->save();
			
			$city->products()->attach($city->id, ['price' => $item['price'], 'is_hit' => (bool)$item['is_hit'], 'data_json' => json_encode($item['data'], JSON_UNESCAPED_UNICODE)]);
		}
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities_products');
    }
}
