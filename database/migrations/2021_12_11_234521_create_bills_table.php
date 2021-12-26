<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
			$table->string('number')->nullable()->comment('номер счета');
			$table->integer('status_id')->default(0)->index()->comment('статус');
			$table->integer('amount')->default(0)->comment('сумма счета');
			$table->integer('deal_id')->default(0)->index()->comment('сделка, по которой выставлен счет');
			$table->integer('deal_position_id')->default(0)->index()->comment('позиция сделки, по которой выставлен счет');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
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
        Schema::dropIfExists('bills');
    }
}
