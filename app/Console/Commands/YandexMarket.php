<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Product;
use App\Models\ProductType;
use App\Repositories\ProductTypeRepository;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Illuminate\Console\Command;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;

class YandexMarket extends Command
{
	private $productTypeRepo;
	
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yandex:market';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export products for Yandex Market';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductTypeRepository $productTypeRepo)
    {
		$this->productTypeRepo = $productTypeRepo;
        parent::__construct();
    }
	
	/**
	 * За сей говнокод 'спасибо' Антону
	 */
    const CATALOG_ALIASES = [
    	'mskregular_30' => 'regular_30',
		'mskregular_60' => 'regular_60',
		'mskregular_90' => 'regular_90',
		'mskregular_120' => 'regular_120',
		'mskregular_180' => 'regular_180',
		'mskultimate_30' => 'ultimate_30',
		'mskultimate_60' => 'ultimate_60',
		'mskultimate_90' => 'ultimate_90',
		'mskultimate_120' => 'ultimate_120',
		'mskultimate_180' => 'ultimate_180',
		'spbregular_30' => 's3589',
		'spbregular_60' => 'r60',
		'spbregular_90' => 'r90',
		'spbregular_120' => 'r120',
		'spbregular_180' => 'r180',
		'spbultimate_30' => 'u30',
		'spbultimate_60' => 'u60',
		'spbultimate_90' => 'u90',
		'spbultimate_120' => 'u120',
		'spbultimate_180' => 'u180',
		'nskregular_30' => 'nvsregular_30',
		'nskregular_60' => 'nvsregular_60',
		'nskregular_90' => 'nvsregular_90',
		'nskregular_120' => 'nvsregular_120',
		'nskregular_180' => 'nvsregular_180',
		'nskultimate_30' => 'nvsultimate_30',
		'nskultimate_60' => 'nvsultimate_60',
		'nskultimate_90' => 'nvsultimate_90',
		'nskultimate_120' => 'nvsultimate_120',
		'nskultimate_180' => 'nvsultimate_180',
		'ekbregular_30' => 'reg_30',
		'ekbregular_60' => 'reg_60',
		'ekbregular_90' => 'reg_90',
		'ekbregular_120' => 'reg_120',
		'ekbregular_180' => 'reg_180',
		'ekbultimate_30' => 'ulti_30',
		'ekbultimate_60' => 'ulti_60',
		'ekbultimate_90' => 'ulti_90',
		'ekbultimate_120' => 'ulti_120',
		'ekbultimate_180' => 'ulti_180',
		'kznregular_30' => 'regu_30',
		'kznregular_60' => 'regu_60',
		'kznregular_90' => 'regu_90',
		'kznregular_120' => 'regu_120',
		'kznregular_180' => 'regu_180',
		'kznultimate_30' => 'ult_30',
		'kznultimate_60' => 'ult_60',
		'kznultimate_90' => 'ult_90',
		'kznultimate_120' => 'ult_120',
		'kznultimate_180' => 'ult_180',
	];
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$cities = City::where('is_active', true)
			->where('version', 'ru')
			->get();
		$products = Product::where('is_active', true)
			->whereRelation('productType', function ($query) {
				$query->whereIn('alias', [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]);
			})
			->orderBy('product_type_id')
			->orderBy('duration')
			->get();
		foreach ($cities as $city) {
			$file = public_path('upload/yandex/' . $city->alias . '.yml');
			$settings = (new Settings())
				->setOutputFile($file)
				->setEncoding('UTF-8');
			
			$shopInfo = (new ShopInfo())
				->setName('Dream Aero ' . $city->alias)
				->setCompany('Dream Aero')
				->setUrl('https://dream-aero.ru');
			
			$currencies = [];
			$currencies[] = (new Currency())
				->setId('RUR')
				->setRate(1);
			
			$categories = [];
			$categories[] = (new Category())
				->setId(1)
				->setName('Сертификаты');
			
			$offers = [];
			foreach ($products as $product) {
				$alias = $city->alias . $product->alias;
				$id = isset(self::CATALOG_ALIASES[$alias]) ? self::CATALOG_ALIASES[$alias] : $alias;

				$offers[] = (new OfferSimple())
					->setId($id)
					->setUrl('https://dream-aero.ru/price')
					->setCategoryId(1)
					->setAvailable(true)
					->addCustomElement('count', 100)
				;
			}
			(new Generator($settings))->generate(
				$shopInfo,
				$currencies,
				$categories,
				$offers
			);
		}
		
        return 0;
    }
}
