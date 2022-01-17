<?php

use App\Models\FlightSimulator;
use App\Models\Location;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals_bills', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('deal_id')->nullable(false)->index();
			$table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
			$table->unsignedBigInteger('bill_id')->nullable(false)->index();
			$table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
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
        Schema::dropIfExists('deals_bills');
    }
}
