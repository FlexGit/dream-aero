<?php

use App\Models\LegalEntity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование юр.лица');
			$table->string('alias')->comment('алиас');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$items = [
			'0' => [
				'name' => 'ООО Демо-компания',
				'alias' => 'demo-company',
				'data' => [
					'public_offer_file_path' => '',
				],
			],
			'1' => [
				'name' => 'ИП Иванов Иван Иванович',
				'alias' => 'ivanov',
				'data' => [
					'public_offer_file_path' => '',
				],
			],
		];
	
		foreach ($items as $item) {
			$legalEntity = new LegalEntity();
			$legalEntity->name = $item['name'];
			$legalEntity->alias = $item['alias'];
			$legalEntity->data_json = $item['data'];
			$legalEntity->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legal_entities');
    }
}
