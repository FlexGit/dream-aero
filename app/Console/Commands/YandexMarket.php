<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Product;
use App\Models\ProductType;
use App\Repositories\ProductTypeRepository;
use Bukashk0zzz\YmlGenerator\Model\Currency;
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$cities = City::where('is_active', true)
			->get();
		$products = Product::where('is_active', true)
			->whereRelation('productType', function ($query) {
				$query->whereIn('alias', [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]);
			})
			->orderBy('product_type_id')
			->orderBy('duration')
			->get();
		
		$file = public_path('upload/yandex/market.yml');
		$settings = (new Settings())
			->setOutputFile($file)
			->setEncoding('UTF-8')
		;
	
		// Creating ShopInfo object
		// (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#shop)
		$shopInfo = (new ShopInfo())
			->setName('Dream Aero')
			->setCompany('Dream Aero')
			->setUrl('https://dream-aero.ru')
		;
	
		// Creating currencies array
		// (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#currencies)
		$currencies = [];
		$currencies[] = (new Currency())
			->setId('RUR')
			->setRate(1)
		;
	
		// Creating categories array
		// (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#categories)
		$categories = [];
		$categories[] = (new Category())
			->setId(1)
			->setName('Certificate')
		;

		// Creating offers array
		// (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#offers)
		$offers = [];
		foreach ($cities as $city) {
			foreach ($products as $product) {
				$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id ?: 1);
				if (!$cityProduct || !$cityProduct->pivot) continue;
				
				$offers[] = (new OfferSimple())
					->setId($city->alias . $product->alias)
					->setAvailable(true)
					->setUrl('https://dream-aero.ru/price')
					->setPrice($cityProduct->pivot->price)
					->setCurrencyId('RUR')
					->setCategoryId(1)
					->setDelivery(true)
					->setName($product->name . ' ' . $city->name);
			}
		}

		(new Generator($settings))->generate(
			$shopInfo,
			$currencies,
			$categories,
			$offers
		);
		
        return 0;
    }
}
