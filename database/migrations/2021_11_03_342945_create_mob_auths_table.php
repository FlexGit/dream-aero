<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
        Schema::create('mob_auths', function (Blueprint $table) {
			$table->id();
            $table->string('token', 200)->index()->comment('токен');
            $table->integer('contractor_id')->index()->comment('контрагент');
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
        Schema::drop('mob_auths');
    }
}
