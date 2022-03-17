<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_contractors', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('contractor_id')->nullable(false)->index();
			$table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
			$table->unsignedBigInteger('notification_id')->nullable(false)->index();
			$table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
			$table->boolean('is_new')->default(1)->comment('является ли уведомление для контрагента новым');
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
        Schema::dropIfExists('notifications_contractors');
    }
}
