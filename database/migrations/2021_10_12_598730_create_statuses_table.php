<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Status;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('наименование');
			$table->string('alias')->comment('алиас');
			$table->string('type', 50)->default('')->index()->comment('тип сущности: контрагент, заказ, сделка, счет, платеж, сертификат');
			$table->integer('sort')->default(0)->comment('сортировка');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
            $table->timestamps();
			$table->softDeletes();
        });
        
        $statuses = [
        	'contractor' => [
        		'gold' => [
        			'name' => 'Золотой',
					'sort' => 30,
					'data' => [
						'flight_time' => 600,
						'discount' => 15,
					],
				],
				'silver' => [
					'name' => 'Серебряный',
					'sort' => 20,
					'data' => [
						'flight_time' => 300,
						'discount' => 10,
					],
				],
				'bronze' => [
					'name' => 'Бронзовый',
					'sort' => 10,
					'data' => [
						'flight_time' => 120,
						'discount' => 5,
					],
				],
				'platinum' => [
					'name' => 'Платиновый',
					'sort' => 40,
					'data' => [
						'flight_time' => 1140,
						'discount' => 20,
					],
				],
			],
			'order' => [
				'received' => [
					'name' => 'Принята',
					'sort' => 10,
					'data' => [
						'color' => '#f0eed8',
					],
				],
				'on_phone' => [
					'name' => 'Недозвон',
					'sort' => 20,
					'data' => [
						'color' => '#fed5a5',
					],
				],
				'processed' => [
					'name' => 'Обработана',
					'sort' => 30,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'canceled' => [
					'name' => 'Отменена',
					'sort' => 40,
					'data' => [
						'color' => '#ffbdba',
					],
				],
			],
			'deal' => [
				'created' => [
					'name' => 'Создана',
					'sort' => 10,
					'data' => [
						'color' => '#f0eed8',
					],
				],
				'calendar' => [
					'name' => 'В календаре',
					'sort' => 30,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'pauseed' => [
					'name' => 'На паузе',
					'sort' => 40,
					'data' => [
						'color' => '#d7baff',
					],
				],
				'canceled' => [
					'name' => 'Отменена',
					'sort' => 50,
					'data' => [
						'color' => '#ffbdba',
					],
				],
			],
			'certificate' => [
				'created' => [
					'name' => 'Создан',
					'sort' => 10,
					'data' => [
						'color' => '#f0eed8',
					],
				],
				'registered' => [
					'name' => 'Зарегистрирован',
					'sort' => 20,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'returned' => [
					'name' => 'Возврат',
					'sort' => 30,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'canceled' => [
					'name' => 'Аннулирован',
					'sort' => 40,
					'data' => [
						'color' => '#ffbdba',
					],
				],
			],
			'bill' => [
				'not_payed' => [
					'name' => 'Не оплачен',
					'sort' => 10,
				],
				'payed' => [
					'name' => 'Оплачен',
					'sort' => 20,
				],
			],
			'payment' => [
				'not_succeed' => [
					'name' => 'Не проведен',
					'sort' => 10,
				],
				'succeed' => [
					'name' => 'Проведен',
					'sort' => 20,
				],
			],
		];
        
        foreach ($statuses as $type => $statusItem) {
        	foreach ($statusItem as $alias => $item) {
				$status = new Status();
				$status->type = $type;
				$status->alias = $alias;
				$status->name = $item['name'];
				$status->sort = $item['sort'];
				$status->data_json = $item['data'] ?? null;
				$status->save();
			}
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
