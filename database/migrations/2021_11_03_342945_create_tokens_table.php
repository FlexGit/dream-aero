<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
        Schema::create('tokens', function (Blueprint $table) {
			$table->id();
            $table->string('token', 200)->index()->comment('токен');
            $table->integer('contractor_id')->index()->comment('контрагент');
			$table->timestamp('expire_at')->nullable()->comment('Действует до');
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
        Schema::drop('tokens');
    }
}
