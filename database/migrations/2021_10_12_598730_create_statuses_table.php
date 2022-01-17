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
			$table->integer('flight_time')->default(0)->comment('время налета');
			$table->integer('sort')->default(0)->comment('сортировка');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
            $table->timestamps();
			$table->softDeletes();
        });
        
        $statuses = [
        	'contractor' => [
        		'contractor_gold' => [
        			'name' => 'Золотой',
					'flight_time' => 600,
					'discount_id' => 3,
					'sort' => 30,
					'data' => [
						'color' => '',
					],
				],
				'contractor_silver' => [
					'name' => 'Серебряный',
					'flight_time' => 300,
					'discount_id' => 2,
					'sort' => 20,
					'data' => [
						'color' => '',
					],
				],
				'contractor_bronze' => [
					'name' => 'Бронзовый',
					'flight_time' => 120,
					'discount_id' => 1,
					'sort' => 10,
					'data' => [
						'color' => '',
					],
				],
				'contractor_platinum' => [
					'name' => 'Платиновый',
					'flight_time' => 1140,
					'discount_id' => 4,
					'sort' => 40,
					'data' => [
						'color' => '',
					],
				],
			],
			/*'order' => [
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
			],*/
			'deal' => [
				'deal_created' => [
					'name' => 'Создана',
					'sort' => 10,
					'data' => [
						'color' => '#f0eed8',
					],
				],
				'deal_confirmed' => [
					'name' => 'Подтверждена',
					'sort' => 20,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'deal_pauseed' => [
					'name' => 'На паузе',
					'sort' => 30,
					'data' => [
						'color' => '#d7baff',
					],
				],
				'deal_returned' => [
					'name' => 'Возврат',
					'sort' => 40,
					'data' => [
						'color' => '#edc9ff',
					],
				],
				'deal_canceled' => [
					'name' => 'Отменена',
					'sort' => 50,
					'data' => [
						'color' => '#ffbdba',
					],
				],
			],
			'certificate' => [
				'certificate_created' => [
					'name' => 'Создан',
					'sort' => 10,
					'data' => [
						'color' => '#f0eed8',
					],
				],
				'certificate_registered' => [
					'name' => 'Зарегистрирован',
					'sort' => 20,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'certificate_canceled' => [
					'name' => 'Аннулирован',
					'sort' => 30,
					'data' => [
						'color' => '#ffbdba',
					],
				],
			],
			'bill' => [
				'bill_not_payed' => [
					'name' => 'Не оплачен',
					'sort' => 10,
					'data' => [
						'color' => '#f0eed8',
					],
				],
				'bill_payed' => [
					'name' => 'Оплачен',
					'sort' => 20,
					'data' => [
						'color' => '#e9ffc9',
					],
				],
				'bill_canceled' => [
					'name' => 'Отменен',
					'sort' => 30,
					'data' => [
						'color' => '#ffbdba',
					],
				],
			],
			/*'payment' => [
				'payment_not_succeed' => [
					'name' => 'Не проведен',
					'sort' => 10,
				],
				'payment_succeed' => [
					'name' => 'Проведен',
					'sort' => 20,
				],
			],*/
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
