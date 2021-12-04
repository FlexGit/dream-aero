<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts_locations', function (Blueprint $table) {
            $table->id();
			$table->integer('discount_id')->default(0)->index();
			$table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
			$table->integer('location_id')->default(0)->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts_locations');
    }
}
