<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование скидки');
			$table->string('alias')->comment('алиас скидки');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->timestamp('active_from_at')->nullable()->comment('дата начала активнсти');
			$table->timestamp('active_to_at')->nullable()->comment('дата окончания активнсти');
			$table->text('data_json')->default('[]')->comment('дополнительная информация');
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
        Schema::dropIfExists('discounts');
    }
}
