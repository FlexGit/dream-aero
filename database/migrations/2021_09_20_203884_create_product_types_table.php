<?php

use App\Models\ProductType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование типа продукта');
			$table->string('alias')->comment('алиас');
			$table->boolean('is_tariff')->default(true)->index()->comment('является ли продукт тарифом');
			$table->integer('sort')->default(0)->comment('сортировка');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$items = [
			'0' => [
				'name' => 'Regular',
				'alias' => 'regular',
				'is_tariff' => 1,
				'data' => [
					'duration' => [30, 60, 90, 120, 180]
				],
			],
			'1' => [
				'name' => 'Ultimate',
				'alias' => 'ultimate',
				'is_tariff' => 1,
				'data' => [
					'duration' => [30, 60, 90, 120, 180]
				],
			],
			'2' => [
				'name' => 'Курсы',
				'alias' => 'courses',
				'is_tariff' => 1,
				'data' => [
					'duration' => [360, 540]
				],
			],
			'3' => [
				'name' => 'VIP полеты',
				'alias' => 'vip',
				'is_tariff' => 1,
				'data' => [
					'duration' => [150]
				],
			],
			'4' => [
				'name' => 'Сопутствующие товары и услуги',
				'alias' => 'services',
				'is_tariff' => 0,
				'data' => [
					'duration' => null
				],
			],
		];
	
		foreach ($items as $item) {
			$productType = new ProductType();
			$productType->name = $item['name'];
			$productType->alias = $item['alias'];
			$productType->is_tariff = (bool)$item['is_tariff'];
			$productType->data_json = $item['data'];
			$productType->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_types');
    }
}
