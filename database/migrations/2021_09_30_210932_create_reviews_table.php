<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('имя');
			$table->text('comment')->nullable()->comment('комментарий');
			$table->text('reply')->nullable()->comment('ответ');
			$table->integer('city_id')->default(0)->index()->comment('город');
			$table->boolean('is_active')->default(false)->index()->comment('признак активности');
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
        Schema::dropIfExists('reviews');
    }
}
