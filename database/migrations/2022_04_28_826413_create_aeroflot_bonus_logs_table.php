<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAeroflotBonusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aeroflot_bonus_logs', function (Blueprint $table) {
			$table->id();
			$table->integer('deal_position_id')->default(0)->index()->comment('позиция сделки');
			$table->string('transaction_order_id')->nullable()->index()->comment('ID транзакции/заказа');
			$table->string('transaction_type')->nullable()->index()->comment('тип транзакции');
			$table->integer('amount')->default(0)->comment('стоимость позиции сделки');
			$table->integer('bonus_amount')->default(0)->comment('Сумма бонуса');
			$table->string('card_number')->nullable()->index()->comment('номер карты');
			$table->string('status', 25)->nullable()->index()->comment('код статуса');
			$table->string('state', 25)->nullable()->index()->comment('код состояние');
			$table->longText('request')->nullable()->comment('тело запроса');
			$table->longText('response')->nullable()->comment('тело ответа');
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
        Schema::dropIfExists('aeroflot_bonus_logs');
    }
}
