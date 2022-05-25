<?php

use App\Models\City;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesPromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities_promocodes', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('promocode_id')->nullable(false)->index();
			$table->foreign('promocode_id')->references('id')->on('promocodes')->onDelete('cascade');
			$table->unsignedBigInteger('city_id')->nullable(false)->index();
			$table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
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
        Schema::dropIfExists('cities_promocodes');
    }
}
