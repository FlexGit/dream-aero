<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\Status;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class RoistatService {
	
	const API_KEY = 'a70f00f181142413d28e389e20cb3837';
	const BASE_URL = 'https://cloud.roistat.com/api/v1';
	const PROJECT_NUMBER = '220597';

	public function __construct()
	{
		$this->headers = [
			'Content-Type' => 'application/json',
			'Api-key' => self::API_KEY,
		];
	}

	public function addDeals()
	{
		$data = [];
		
		// Сделки с номером визита Roistat, созданные или измененные за последние сутки
		$deals = Deal::whereNotNull('roistat')
			->where('updated_at', '>=', Carbon::now()->subDay())
			->oldest()
			->get();
		
		$i = 0;
		
		/** @var Deal[] $deals */
		foreach ($deals as $deal) {
			$dealStatus = $deal->status;
			$positions = $deal->positions;
			$user = $deal->user;
			$dealCity = $deal->city;
			
			$data[$i] = [
				'id' => (string)$deal->id,
				'name' => $deal->number,
				'date_create' => $deal->created_at,
				'status' => $dealStatus ? (string)$dealStatus->id : 0,
				'roistat' => $deal->roistat,
				'price' => (string)$deal->amount(),
				'client_id' => (string)$deal->contractor_id,
				'fields' => [
					'name' => $deal->name,
					'phone' => $deal->phoneFormatted(),
					'email' => $deal->email,
					'source' => Deal::SOURCES[$deal->source],
				],
			];
			if ($user) {
				$data[$i]['fields']['user'] = $user->fioFormatted();
			}
			if ($dealCity) {
				$data[$i]['fields']['city'] = $dealCity->name;
			}
			
			$data[$i]['products'] = [];
			$j = 0;
			
			/** @var DealPosition[] $positions */
			foreach ($positions as $position) {
				$product = $position->product;
				if (!$product) continue;
				
				$productType = $product->productType;
				if (!$productType) continue;
				
				$data[$i]['products'][$j] = [
					'id' => (string)$product->id,
					'name' => $product->name,
					'quantity' => 1,
					'price' => $position->amount,
					'category' => [
						'level1' => $productType->name,
					],
				];
			}
			
			++$i;
		}
		
		try {
			$response = Http::acceptJson()
				->timeout(5)
				->withHeaders($this->headers)
				->post(self::BASE_URL . '/project/add-orders?project=' . self::PROJECT_NUMBER, $data)
				->throw();
			//$result = $response->body();
			
			\Log::channel('roistat')->info(__METHOD__ . 'Request: ' . json_encode($data) . ' : Response: ' . $response);
			
			return null;
		} catch (\Throwable $e) {
			\Log::channel('roistat')->info(__METHOD__ . ': ' . $e->getMessage());
		}
		
		return null;
	}
	
	public function setStatuses()
	{
		$data = [];
		
		// Статусы Сделки
		$statuses = Status::where('type', Status::STATUS_TYPE_DEAL)
			->oldest()
			->get();
		
		/** @var Status[] $statuses */
		foreach ($statuses as $status) {
			switch ($status->alias) {
				case Deal::CONFIRMED_STATUS:
					$type = 'paid';
				break;
				case Deal::RETURNED_STATUS:
				case Deal::CANCELED_STATUS:
					$type = 'canceled';
				break;
				default:
					$type = 'progress';
				break;
			}
			
			$data[] = [
				'id' => (string)$status->id,
				'name' => $status->name,
				'type' => $type,
			];
		}
		
		try {
			$response = Http::acceptJson()
				->timeout(5)
				->withHeaders($this->headers)
				->post(self::BASE_URL . '/project/set-statuses?project=' . self::PROJECT_NUMBER, $data)
				->throw();
			$result = $response->body();
			
			\Log::channel('roistat')->info(__METHOD__ . 'Request: ' . json_encode($data) . ' : Response: ' . $response);
			
			return null;
		} catch (\Throwable $e) {
			\Log::channel('roistat')->info(__METHOD__ . ': ' . $e->getMessage());
		}
		
		return null;
	}
	
	public function updateDealStatus(Deal $deal)
	{
		$data = [
			'status_id' => $deal->status_id,
		];
		
		try {
			$response = Http::acceptJson()
				->timeout(5)
				->withHeaders($this->headers)
				->post(self::BASE_URL . '/project/integration/order/' . $deal->id . '/status/update?project=' . self::PROJECT_NUMBER, $data)
				->throw();
			$result = $response->body();
			
			\Log::channel('roistat')->info(__METHOD__ . 'Request: ' . json_encode($data) . ' : Response: ' . $response);
			
			return null;
		} catch (\Throwable $e) {
			\Log::channel('roistat')->info(__METHOD__ . ': ' . $e->getMessage());
		}
		
		return null;
	}
}