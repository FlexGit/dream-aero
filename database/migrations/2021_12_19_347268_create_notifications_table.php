<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
			$table->string('title')->comment('заголовок');
			$table->text('description')->comment('описание');
			$table->integer('city_id')->default(0)->index()->comment('город');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент');
			$table->boolean('is_new')->default(true)->index()->comment('новое уведомление');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
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
        Schema::dropIfExists('notifications');
    }
}
