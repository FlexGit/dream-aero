<?php

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
			$table->integer('city_id')->default(0)->index()->comment('город, в котором действует продукт');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->integer('price')->default(0)->comment('базовая цена продукта');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
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
        Schema::dropIfExists('products');
    }
}
