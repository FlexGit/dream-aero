<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts_cities', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('discount_id')->nullable(false)->index();
			$table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
			$table->unsignedBigInteger('city_id')->nullable(false)->index();
			$table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts_cities');
    }
}
