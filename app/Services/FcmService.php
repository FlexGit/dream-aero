<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Http;

class FcmService {
	
	/**
	 * @param Notification $notification
	 * @param $fcmTokens
	 * @return bool
	 */
	public static function send(Notification $notification, $fcmTokens)
	{
		$headers = [
			'Authorization: key=' . env('FCM_SERVER_KEY'),
			'Content-Type: application/json',
		];

		$data = [
			'registration_ids' => $fcmTokens,
			'notification' => [
				'title' => $notification->title,
				'body' => $notification->body,
			]
		];
		
		try {
			$response = Http::acceptJson()
				->timeout(5)
				->withHeaders($headers)
				->post('https://fcm.googleapis.com/fcm/send', $data)
				->throw();
			
			\Log::channel('fcm')->info(__METHOD__ . 'Request: ' . json_encode($data) . ' : Response: ' . $response);
			
			return true;
		} catch (\Throwable $e) {
			\Log::channel('fcm')->info(__METHOD__ . ': ' . $e->getMessage());

			return false;
		}
	}
}