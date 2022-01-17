<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations_products', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('location_id')->nullable(false)->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			$table->unsignedBigInteger('product_id')->nullable(false)->index();
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('locations_products');
    }
}
