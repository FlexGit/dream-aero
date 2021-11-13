<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
        Schema::create('promos', function (Blueprint $table) {
			$table->id();
            $table->string('name')->comment('наименование');
            $table->text('preview_text')->nullable()->comment('анонс');
            $table->text('detail_text')->nullable()->comment('описание');
			$table->integer('city_id')->default(0)->index()->comment('город, к которому относится акция');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
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
        Schema::drop('promos');
    }
}
