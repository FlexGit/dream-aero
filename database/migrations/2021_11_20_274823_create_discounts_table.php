<?php

use App\Models\Discount;
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
            $table->string('value')->comment('размер скидки');
			$table->boolean('is_fixed')->default(true)->index()->comment('фиксированная скидка');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$discounts = [
			'0' => [
				'value' => '5',
				'is_fixed' => false,
			],
			'1' => [
				'value' => '10',
				'is_fixed' => false,
			],
			'2' => [
				'value' => '15',
				'is_fixed' => false,
			],
			'3' => [
				'value' => '20',
				'is_fixed' => false,
			],
			'4' => [
				'value' => '25',
				'is_fixed' => false,
			],
			'5' => [
				'value' => '30',
				'is_fixed' => false,
			],
			'6' => [
				'value' => '35',
				'is_fixed' => false,
			],
			'7' => [
				'value' => '40',
				'is_fixed' => false,
			],
			'8' => [
				'value' => '45',
				'is_fixed' => false,
			],
			'9' => [
				'value' => '50',
				'is_fixed' => false,
			],
			'10' => [
				'value' => '500',
				'is_fixed' => true,
			],
			'11' => [
				'value' => '1000',
				'is_fixed' => true,
			],
			'12' => [
				'value' => '1500',
				'is_fixed' => true,
			],
			'13' => [
				'value' => '2000',
				'is_fixed' => true,
			],
			'14' => [
				'value' => '2500',
				'is_fixed' => true,
			],
			'15' => [
				'value' => '3000',
				'is_fixed' => true,
			],
		];
	
		foreach ($discounts as $item) {
			$discount = new Discount();
			$discount->value = $item['value'];
			$discount->is_fixed = (bool)$item['is_fixed'];
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
