<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills_positions', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('bill_id')->nullable(false)->index();
			$table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
			$table->unsignedBigInteger('deal_position_id')->nullable(false)->index();
			$table->foreign('deal_position_id')->references('id')->on('deal_positions')->onDelete('cascade');
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
        Schema::dropIfExists('bills_positions');
    }
}
