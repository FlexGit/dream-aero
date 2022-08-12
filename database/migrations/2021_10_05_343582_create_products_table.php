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
			$table->integer('duration')->default(0)->comment('длительность полёта, мин.');
			$table->integer('validity')->default(0)->comment('количество месяцев действия сертификата');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
            $table->timestamps();
			$table->softDeletes();
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
