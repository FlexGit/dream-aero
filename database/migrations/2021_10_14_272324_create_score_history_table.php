<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoreHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_history', function (Blueprint $table) {
            $table->id();
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, которому начислены баллы');
			$table->integer('score')->default(0)->comment('сумма баллов');
			$table->integer('created_by_user_id')->default(0)->index()->comment('пользователь, начисливший баллы');
			$table->text('data_json')->default('')->comment('дополнительная информация: комментарий');
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
        Schema::dropIfExists('score_history');
    }
}
