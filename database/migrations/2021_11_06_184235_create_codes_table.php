<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
        Schema::create('codes', function (Blueprint $table) {
			$table->id();
            $table->char('code', 4)->index()->comment('код подтверждения');
            $table->string('email')->comment('E-mail');
            $table->integer('contractor_id')->index()->default(0)->comment('Контрагент');
			$table->boolean('is_reset')->default(false)->index()->comment('признак использования');
			$table->timestamp('reset_at')->nullable()->comment('дата использования кода подтверждения');
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
        Schema::drop('codes');
    }
}
