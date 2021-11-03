<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование юр.лица');
			$table->text('data_json')->default('')->comment('дополнительная информация');
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
        Schema::dropIfExists('legal_entities');
    }
}
