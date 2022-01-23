<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
			$table->string('name', 25)->comment('наименование');
			$table->string('alias', 25)->comment('alias');
            $table->timestamps();
			$table->softDeletes();
        });

		$items = [];

		$items[] = [
			'name' => 'руб',
			'alias' => 'RUB',
		];
		$items[] = [
			'name' => '$',
			'alias' => 'USD',
		];

		foreach ($items as $item) {
			$currency = new Currency();
			$currency->name = $item['name'];
			$currency->alias = $item['alias'];
			$currency->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
