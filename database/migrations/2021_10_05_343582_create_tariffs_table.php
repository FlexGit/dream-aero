<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование тарифа');
			$table->integer('tariff_type_id')->default(0)->index()->comment('тип тарифа');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором действует продукт');
			$table->integer('duration')->comment('длительность полёта, мин.');
			$table->text('data_json')->comment('дополнительная информация');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->integer('price')->default(0)->comment('базовая цена продукта');
			$table->boolean('is_hit')->default(0)->comment('является ли продукт хитом продаж');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
}
