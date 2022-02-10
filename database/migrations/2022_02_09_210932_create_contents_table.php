<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->text('title')->comment('заголовок');
			$table->text('alias')->comment('алиас');
			$table->longText('detail_text')->nullable()->comment('подробно');
			$table->text('preview_text')->nullable()->comment('превью');
			$table->integer('parent_id')->default(0)->index()->comment('родитель');
			$table->string('version', 25)->default('ru')->index()->comment('версия');
			$table->text('meta_title')->nullable()->comment('meta Title');
			$table->text('meta_description')->nullable()->comment('meta Description');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->timestamp('published_at')->nullable()->comment('дата публикации');
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
        Schema::dropIfExists('contents');
    }
}
