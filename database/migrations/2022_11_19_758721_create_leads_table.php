<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
			$table->id();
			$table->string('type', 50)->nullable()->index()->comment('тип лида');
			$table->string('name')->nullable()->comment('имя');
			$table->string('phone')->nullable()->comment('телефон');
			$table->string('email')->nullable()->comment('email');
			$table->integer('product_id')->default(0)->index()->comment('продукт');
			$table->integer('city_id')->default(0)->index()->comment('город');
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
        Schema::dropIfExists('leads');
    }
}
