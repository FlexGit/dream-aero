<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
			$table->integer('status_id')->default(0)->index()->comment('статус бронирования');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, совершивший бронирование');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->timestamp('filght_at')->nullable()->comment('дата и время полета');
			$table->integer('tariff_id')->default(0)->index()->comment('тариф, по которому будет осуществлен полет');
			$table->timestamp('invite_sent_at')->nullable()->comment('последняя дата отправки приглашения на e-mail');
			$table->integer('created_by_user_id')->default(0)->index()->comment('пользователь, создавший бронирование');
			$table->integer('updated_by_user_id')->default(0)->index()->comment('пользователь, изменивший последним бронирование');
			$table->text('data_json')->default('')->comment('дополнительная информация: комментарий к бронированию');
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
        Schema::dropIfExists('bookings');
    }
}
