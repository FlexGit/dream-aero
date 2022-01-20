<?php

use App\Models\City;
use App\Models\Location;
use App\Models\LegalEntity;
use App\Services\HelpFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('наименование локации');
			$table->string('alias', 50)->comment('alias');
			$table->integer('legal_entity_id')->default(0)->index()->comment('юр.лицо, на которое оформлена локация');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором находится локация');
			$table->integer('sort')->default(0)->comment('сортировка');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$ryazhkoLegalEntity = HelpFunctions::getEntityByAlias(LegalEntity::class, LegalEntity::RYAZHKO_ALIAS);
		$murinLegalEntity = HelpFunctions::getEntityByAlias(LegalEntity::class, LegalEntity::MURIN_ALIAS);
	
		$items = [];
	
		// Москва
		$city = HelpFunctions::getEntityByAlias(City::class, City::MSK_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Афимолл Сити"',
			'alias' => 'afi',
			'legal_entity_id' => $murinLegalEntity ? $murinLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => '123317, Москва, ТРЦ "Афимолл Сити" (Пресненская наб., 2) 6 этаж, м. Выставочная',
				'working_hours' => 'Пн-Чт: 10:00 – 22:00<br>Пт-Сб/праздники: 10:00 – 23:00<br>Вс: 10:00 – 22:00',
				'phone' => '+7 (495) 642-46-15',
				'email' => 'msk@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2245.5400983497398!2d37.53734031542885!3d55.749119399744195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54bdce1d3d3b5%3A0xfd349dcf575adf73!2sAfimall+City!5e0!3m2!1sen!2sru!4v1519541409582',
				'skype' => 'info.dream.aero.msk',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/msk_afi.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '94759121',
				],
			],
		];
		$items[] = [
			'name' => 'ТРК "VEGAS Кунцево"',
			'alias' => 'veg',
			'legal_entity_id' => $murinLegalEntity ? $murinLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 20,
			'data' => [
				'address' => '143025, Москва, ТРК "VEGAS Кунцево", 56-й км МКАД, 1 этаж',
				'working_hours' => 'ежедневно с 10:00 до 23:00',
				'phone' => '+7 (495) 642-23-60',
				'email' => 'msk@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2247.20271145491!2d37.379402191004054!3d55.7202292482709!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x7897b00be1eba0d6!2sVegas+Kuntsevo!5e0!3m2!1sen!2sru!4v1536552812345',
				'skype' => 'dream.aero.vegas',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/msk_veg.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '44741905',
				],
			],
		];
		$items[] = [
			'name' => 'ТРЦ "Columbus"',
			'alias' => 'bus',
			'legal_entity_id' => $ryazhkoLegalEntity ? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 30,
			'data' => [
				'address' => '117519, Москва, ТРЦ "Columbus", ул.Кировоградская, д.13А, 3 этаж',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (495) 740-27-67',
				'email' => 'msk@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d4506.841412093567!2d37.607302!3d55.612095!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xab8e8ff10878a096!2z0KLQoNCmIENvbHVtYnVz!5e0!3m2!1sru!2sru!4v1579536377500!5m2!1sru!2sru',
				'skype' => null,
				'whatsapp' => null,
				'scheme_file_path' => null,
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '51021517',
				],
			],
		];

		// Санкт-Петербург
		$city = HelpFunctions::getEntityByAlias(City::class, City::SPB_ALIAS);
		
		$items[] = [
			'name' => 'ТРК "РИО"',
			'alias' => 'rio',
			'legal_entity_id' => $murinLegalEntity ? $murinLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => '192102, Санкт-Петербург, ТРК "РИО" (ул. Фучика д.2).',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (812) 937-84-17',
				'email' => 'info@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2002.3137930911805!2d30.356641315621054!3d59.87713967402086!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46962fe18138eaf1%3A0x1eb363de1e313f6a!2zVHJrICJSaW8i!5e0!3m2!1sen!2sru!4v1519541442515',
				'skype' => 'dream.aero',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/spb_rio.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '15136727',
				],
			],
		];
		$items[] = [
			'name' => 'ТРЦ "Охта Молл"',
			'alias' => 'ohta',
			'legal_entity_id' => $murinLegalEntity ? $murinLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 20,
			'data' => [
				'address' => '195027, Санкт-Петербург, ТРЦ "Охта Молл" (Брантовская дор., 3), 3 этаж',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (812) 906-20-44',
				'email' => 'info@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1998.5148908802503!2d30.415812015618517!3d59.940191468935964!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x469631f1a81e6de1%3A0x8d2b957976db2628!2sOkhta+Moll!5e0!3m2!1sen!2sru!4v1529668400119',
				'skype' => 'dream.aero',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/spb_ohta.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '35413212',
				],
			],
		];
		$items[] = [
			'name' => 'ТРК "ПИТЕРЛЭНД"',
			'alias' => 'land',
			'legal_entity_id' => $murinLegalEntity ? $murinLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 30,
			'data' => [
				'address' => '192102, Санкт-Петербург, ТРК "ПИТЕРЛЭНД" (Приморский пр., д. 72)',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (812) 913-45-13',
				'email' => 'info@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1996.0205664521582!2d30.208570116174872!3d59.98156886559679!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4696368be588c349%3A0x8c021258c4435546!2z0J_QuNGC0LXRgNC70Y3QvdC0!5e0!3m2!1sru!2sru!4v1618821054835!5m2!1sru!2sru',
				'skype' => 'dream.aero',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/spb_piterland.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '74346719',
				],
			],
		];
	
		// Воронеж
		$city = HelpFunctions::getEntityByAlias(City::class,City::VRN_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Центр Галереи Чижова"',
			'alias' => 'vrn',
			'legal_entity_id' => $murinLegalEntity ? $murinLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'ТРЦ "Центр Галереи Чижова", ул. Кольцовская, 35, Воронеж, Воронежская обл., 394030',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (920) 459-10-99',
				'email' => 'vrn@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d9898.626206617424!2d39.1915983!3d51.6661243!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x3963c980f1fa5e64!2z0KbQtdC90YLRgCDQk9Cw0LvQtdGA0LXQuCDQp9C40LbQvtCy0LA!5e0!3m2!1sru!2sru!4v1607393932934!5m2!1sru!2sru',
				'skype' => null,
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/vrn_chizh.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '42236168',
				],
			],
		];
	
		// Казань
		$city = HelpFunctions::getEntityByAlias(City::class, City::KZN_ALIAS);
	
		$items[] = [
			'name' => 'ТРК "Парк Хаус"',
			'alias' => 'kzn',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'ТРК "Парк Хаус" 1 этаж, пр-т. Хусаина Ямашева, 46/33, Казань, Республика Татарстан, 420124',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (843) 203-21-63',
				'email' => 'kzn@dream-aero.com',
				'map_link' => 'https://maps.google.com/maps?q=%D0%BF%D1%80-%D1%82.%20%D0%A5%D1%83%D1%81%D0%B0%D0%B8%D0%BD%D0%B0%20%D0%AF%D0%BC%D0%B0%D1%88%D0%B5%D0%B2%D0%B0,%2046/33,%20%D0%9A%D0%B0%D0%B7%D0%B0%D0%BD%D1%8C,%20%D0%A0%D0%B5%D1%81%D0%BF%D1%83%D0%B1%D0%BB%D0%B8%D0%BA%D0%B0%20%D0%A2%D0%B0%D1%82%D0%B0%D1%80%D1%81%D1%82%D0%B0%D0%BD,%20420124&t=&z=15&ie=UTF8&iwloc=&output=embed',
				'skype' => null,
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/kzn_parkhouse.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '73949458',
				],
			],
		];
	
		// Краснодар
		$city = HelpFunctions::getEntityByAlias(City::class, City::KRD_ALIAS);
	
		$items[] = [
			'name' => 'ТРК "СБС Мегамолл"',
			'alias' => 'krd',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'ТРК "СБС Мегамолл", ул. Уральская, 79/1, Краснодар, Краснодарский край, 350059',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (861) 290-43-90',
				'email' => 'krd@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2819.521367633347!2d39.050085415543165!3d45.034640879098205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40f05aae37e98165%3A0xcff0286447aa27f5!2sSbs+Megamoll!5e0!3m2!1sen!2sru!4v1519666313454',
				'skype' => 'krd_204',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/krd_megamall.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '63552356',
				],
			],
		];
	
		// Нижний Новгород
		$city = HelpFunctions::getEntityByAlias(City::class, City::NNV_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Жар-Птица"',
			'alias' => 'nnv',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'ТРЦ "Жар-Птица", Советская пл., 5, Нижний Новгород, Нижегородская обл., 603122',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (831) 283-42-20',
				'email' => 'nnv@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2213.945812898895!2d44.03979441595176!3d56.296248180702236!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4151d4e1e1c9d47d%3A0x84d695b3d61cfb64!2sZhar+Ptitsa!5e0!3m2!1sen!2sru!4v1561346829507!5m2!1sen!2sru',
				'skype' => 'Dream Aero NN',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/nnv_zharptitsa.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '97109513',
				],
			],
		];
	
		// Самара
		$city = HelpFunctions::getEntityByAlias(City::class, City::SAM_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Космопорт"',
			'alias' => 'sam',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'ТРЦ "Космопорт", ул. Дыбенко, 30, Самара, Самарская обл., 443086',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (846) 225-02-45',
				'email' => 'sam@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2389.5754398912322!2d50.1955732159726!3d53.20752999280083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x41661c1d5d6be3ef%3A0xd3459c5ed87271cf!2z0JrQvtGB0LzQvtC_0L7RgNGC!5e0!3m2!1sru!2sru!4v1611646551660!5m2!1sru!2sru',
				'skype' => null,
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/sam_kosmoport.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '10866784',
				],
			],
		];
	
		// Екатеринбург
		$city = HelpFunctions::getEntityByAlias(City::class, City::EKB_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Алатырь"',
			'alias' => 'ekb',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => '620014, Екатеринбург, ТРЦ "Алатырь", ул. Малышева, 5, 3 этаж',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (343) 361-38-04',
				'email' => 'ekb@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2182.7806506141264!2d60.580142215972664!3d56.832552380855866!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x43c16ef70eb46a1f%3A0xff3ad485702da0dc!2sAlatyr\'!5e0!3m2!1sen!2sru!4v1519541351078',
				'skype' => 'info.dream.aero.ekb',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/ekb_alatyr.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '64495138',
				],
			],
		];
	
		// Новосибириск
		$city = HelpFunctions::getEntityByAlias(City::class, City::NSK_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Сибирский молл"',
			'alias' => 'nsk',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => '630112, Новосибирск, ТРЦ "Сибирский молл" (ул. Фрунзе, 238)',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (383) 375-23-10',
				'email' => 'nsk@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4572.529314388402!2d82.95664226202307!3d55.038578534369506!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x42dfe5f3f1bb89ed%3A0xab2c73ce3173def8!2z0KHQuNCx0LjRgNGB0LrQuNC5INCc0L7Qu9C7!5e0!3m2!1sru!2sru!4v1539285445811',
				'skype' => 'Dream Aero SIBMAII',
				'whatsapp' => null,
				'scheme_file_path' => 'scheme/nsk_siberianmall.webp',
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '84548202',
				],
			],
		];
	
		// Хабаровск
		$city = HelpFunctions::getEntityByAlias(City::class, City::KHV_ALIAS);
	
		$items[] = [
			'name' => 'ТРЦ "Brosko Mall"',
			'alias' => 'khv',
			'legal_entity_id' => $ryazhkoLegalEntity? $ryazhkoLegalEntity->id : 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'ТРЦ "Brosko Mall", ул. Пионерская, 2В, 3 этаж',
				'working_hours' => 'ежедневно с 10:00 до 22:00',
				'phone' => '+7 (4212) 942-732',
				'email' => 'khv@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2645.710647559121!2d135.07682566566035!3d48.46208107925066!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5efae9639279bc27%3A0xf22a5378bb44edd2!2z0JHRgNC-0YHQutC-INCc0L7Qu9C7!5e0!3m2!1sru!2s!4v1624907186224!5m2!1sru!2s',
				'skype' => null,
				'whatsapp' => null,
				'scheme_file_path' => null,
				'pay_system' => [
					'alias' => 'moneta',
					'account_number' => '38672185',
				],
			],
		];
	
		// Dubai
		$city = HelpFunctions::getEntityByAlias(City::class, City::UAE_ALIAS);
	
		$items[] = [
			'name' => 'Festival City Mall',
			'alias' => 'uae',
			'legal_entity_id' => 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'Dubai Festival City Mall
Ground Floor, next to Centrepoint Dubai, UAE',
				'working_hours' => 'Sunday - Thursday - 10 AM - 12 PM<br>Thursday - Friday - 10 AM - 01 PM',
				'phone' => '+971 4 224 9987',
				'email' => 'dubai@dream-aero.com',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3609.4452858227983!2d55.3499096143887!3d25.221922936887697!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f5d778cffffff%3A0xf49b6296189d22d5!2sDubai+Festival+City+Mall!5e0!3m2!1sen!2sru!4v1526977093496',
				'skype' => null,
				'whatsapp' => '00971505587151',
				'scheme_file_path' => null,
				'pay_system' => [
					'alias' => null,
					'account_number' => null,
				],
			],
		];
	
		// Washington D.C.
		$city = HelpFunctions::getEntityByAlias(City::class, City::DC_ALIAS);
	
		$items[] = [
			'name' => 'WESTFIELD Montgomery Mall',
			'alias' => 'usa',
			'legal_entity_id' => 0,
			'city_id' => $city ? $city->id : 0,
			'sort' => 10,
			'data' => [
				'address' => 'WESTFIELD Montgomery Mall<br>7101 Democracy Boulevard Store No. 3100 Bethesda MD 20817',
				'working_hours' => 'Sunday 11:00 AM to 7:00 PM<br>Monday - Thursday 12:00 PM to 8:00 PM<br>Friday,Saturday 11:00 AM to 8:00 PM',
				'phone' => '+1 240 224 48 85',
				'email' => 'dc@dream.aero',
				'map_link' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d12398.45561714155!2d-77.1459884!3d39.0241201!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xa8ce96213e429d83!2sWestfield+Montgomery!5e0!3m2!1sen!2sru!4v1557729373131!5m2!1sen!2sru',
				'skype' => null,
				'whatsapp' => '00971505587151',
				'scheme_file_path' => 'scheme/dc_westfield.webp',
				'pay_system' => [
					'alias' => null,
					'account_number' => null,
				],
			],
		];

		foreach ($items as $item) {
			$location = new Location();
			$location->name = $item['name'];
			$location->alias = $item['alias'];
			$location->legal_entity_id = $item['legal_entity_id'];
			$location->city_id = $item['city_id'];
			$location->sort = $item['sort'];
			$location->data_json = $item['data'];
			$location->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
