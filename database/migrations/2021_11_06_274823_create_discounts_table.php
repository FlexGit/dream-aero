<?php

use App\Models\Currency;
use App\Models\Discount;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->integer('value')->nullable()->comment('размер скидки');
			$table->string('alias', 50)->nullable()->comment('алиас');
			$table->boolean('is_fixed')->default(true)->index()->comment('фиксированная скидка');
			$table->integer('currency_id')->default(0)->index()->comment('валюта');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
            $table->timestamps();
			$table->softDeletes();
        });

		$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);

		$discounts = [];

		$discounts[] = [
			'value' => '5',
			'alias' => Discount::DISCOUNT_5_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '10',
			'alias' => Discount::DISCOUNT_10_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '15',
			'alias' => Discount::DISCOUNT_15_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '20',
			'alias' => Discount::DISCOUNT_20_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '25',
			'alias' => Discount::DISCOUNT_25_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '30',
			'alias' => Discount::DISCOUNT_30_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '35',
			'alias' => Discount::DISCOUNT_35_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '40',
			'alias' => Discount::DISCOUNT_40_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '45',
			'alias' => Discount::DISCOUNT_45_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '50',
			'alias' => Discount::DISCOUNT_50_ALIAS,
			'is_fixed' => false,
			'currency_id' => 0,
		];
		$discounts[] = [
			'value' => '500',
			'alias' => Discount::DISCOUNT_FIXED_500_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];
		$discounts[] = [
			'value' => '1000',
			'alias' => Discount::DISCOUNT_FIXED_1000_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];
		$discounts[] = [
			'value' => '1500',
			'alias' => Discount::DISCOUNT_FIXED_1500_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];
		$discounts[] = [
			'value' => '2000',
			'alias' => Discount::DISCOUNT_FIXED_2000_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];
		$discounts[] = [
			'value' => '2500',
			'alias' => Discount::DISCOUNT_FIXED_2500_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];
		$discounts[] = [
			'value' => '3000',
			'alias' => Discount::DISCOUNT_FIXED_3000_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];
		$discounts[] = [
			'value' => '5000',
			'alias' => Discount::DISCOUNT_FIXED_5000_ALIAS,
			'is_fixed' => true,
			'currency_id' => $currency->id ?? 0,
		];

		foreach ($discounts as $item) {
			$discount = new Discount();
			$discount->value = $item['value'];
			$discount->alias = $item['alias'];
			$discount->is_fixed = (bool)$item['is_fixed'];
			$discount->currency_id = $item['currency_id'];
			$discount->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
