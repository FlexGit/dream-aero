<?php

namespace App\Services;

use App\Models\LegalEntity;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Illuminate\Support\Carbon;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use App\Models\Deal;
use App\Models\Payment;

class SbisService {
	
	const BASE_URL = 'https://api.sbis.ru';
	const LEGAL_ENTITY_AUTH_DATA = [
		'kornyskov-murin' => [
			'app_client_id' => '1688289885064718',
			'login' => 'kassa737',
			'password' => 'Kassa737b',
		],
		'ryazhko' => [
			'app_client_id' => '1688289885064718', // ToDO заменить
			'login' => 'ekaterina.e@dream-aero.com',
			'password' => 'emasheva88',
		],
	];

	//private static $log = null;

	protected $legalEntity;

	public function __construct(LegalEntity $legalEntity)
	{
		if (!array_key_exists($legalEntity->alias, self::LEGAL_ENTITY_AUTH_DATA)) {
			throw new \Exception('Invalid $alias "' . $legalEntity->alias . '"');
		}
		if (!$legalEntity->inn) {
			throw new \Exception('Invalid $inn "' . $legalEntity->inn . '"');
		}

		$this->alias = $legalEntity->alias;
		$this->inn = $legalEntity->inn;

		$this->stack = HandlerStack::create();
		$this->stack->push(
			Middleware::log(
				new Logger('Sbis Logger'),
				new MessageFormatter('{req_body} - {res_body}')
			)
		);
	}

	public function auth()
	{
		try {
			$client = new Client(
				[
					'base_url' => self::BASE_URL,
					'handler' => $this->stack,
				]
			);
			$result = $client->post('/oauth/service/', [
				'app_client_id' => self::LEGAL_ENTITY_AUTH_DATA[$this->alias]['app_client_id'],
				'login' => self::LEGAL_ENTITY_AUTH_DATA[$this->alias]['login'],
				'password' => self::LEGAL_ENTITY_AUTH_DATA[$this->alias]['password'],
			]);
			$httpCode = $result->getStatusCode();
			$response = (string)$result->getBody()->getContents();

			//self::logInfo(__FUNCTION__ . ' ' . __CLASS__ . ' RESPONSE', ['code' => $httpCode, 'response' => $response]);

			$response = json_decode($response, true);
			
			return null;
		} catch (\Throwable $e) {
			//self::logError('Cannot send ' . __FUNCTION__ . ' ' . __CLASS__ . ' request: ' . $e->getMessage(), ['DocumentsBatchId' => $data['DocumentsBatchId'] ?? '-']);
		}
		
		return null;
	}

	public function getKkts()
	{
		try {
			$client = new Client(
				[
					'base_url' => self::BASE_URL,
					'handler' => $this->stack,
				]
			);
			$result = $client->get('/ofd/v1/orgs/' . $this->inn . '/kkts?status=2');
			$httpCode = $result->getStatusCode();
			$response = (string)$result->getBody()->getContents();

			$kkts = json_decode($response, true);

			$kktIds = [];
			foreach ($kkts ?? [] as $kkt) {
				if (!$kkt['regId']) continue;

				$kktIds[] = $kkt['regId'];
			}

			return $kktIds;
		} catch (\Exception $e) {
			//self::logError('Cannot send ' . __FUNCTION__ . ' ' . __CLASS__ . ' request: ' . $e->getMessage(), ['DocumentsBatchId' => $data['DocumentsBatchId'] ?? '-']);
		}

		return null;
	}

	public function getStorages($kktRegId)
	{
		try {
			$client = new Client(
				[
					'base_url' => self::BASE_URL,
					'handler' => $this->stack,
				]
			);
			$result = $client->get('/ofd/v1/orgs/' . $this->inn . '/kkts/'. $kktRegId .'/storages?status=2');
			$httpCode = $result->getStatusCode();
			$response = (string)$result->getBody()->getContents();

			$storages = json_decode($response, true);

			$storageIds = [];
			foreach ($storages ?? [] as $storage) {
				if (!$storage['storageId']) continue;

				$storageIds[] = $storage['storageId'];
			}

			return $storageIds;
		} catch (\Exception $e) {
			//self::logError('Cannot send ' . __FUNCTION__ . ' ' . __CLASS__ . ' request: ' . $e->getMessage(), ['DocumentsBatchId' => $data['DocumentsBatchId'] ?? '-']);
		}

		return null;
	}

	public function getReceipts($kktRegId, $storageId, $dateFrom, $dateTo)
	{
		try {
			if (!$dateFrom) {
				$dateFrom = Carbon::today()->startOfDay();
			}
			if (!$dateTo) {
				$dateTo = Carbon::today()->endOfDay();
			}

			$client = new Client(
				[
					'base_url' => self::BASE_URL,
					'handler' => $this->stack,
				]
			);
			$result = $client->get('/ofd/v1/orgs/' . $this->inn . '/kkts/'. $kktRegId .'/storages/'. $storageId .'/docs?dateFrom='.$dateFrom.'&dateTo='.$dateTo);
			$httpCode = $result->getStatusCode();
			$response = (string)$result->getBody()->getContents();

			$docs = json_decode($response, true);

			$receipts = [];
			foreach ($docs ?? [] as $docType => $doc) {
				if ($docType != 'receipt') continue;

				$receipts[] = $doc;
			}

			return $receipts;
		} catch (\Exception $e) {
			//self::logError('Cannot send ' . __FUNCTION__ . ' ' . __CLASS__ . ' request: ' . $e->getMessage(), ['DocumentsBatchId' => $data['DocumentsBatchId'] ?? '-']);
		}

		return null;
	}
}