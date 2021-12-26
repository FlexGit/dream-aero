<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_discounts', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('location_id')->nullable(false)->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			$table->unsignedBigInteger('discount_id')->nullable(false)->index();
			$table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_discounts');
    }
}
