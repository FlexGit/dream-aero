<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
			$table->string('number')->nullable()->comment('номер сделки');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, с которым заключена сделка');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');            $table->timestamps();
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
        Schema::dropIfExists('deals');
    }
}
