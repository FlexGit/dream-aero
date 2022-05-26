<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\DealPosition;
use App\Models\PlatformLog;
use Illuminate\Http\Request;
use App\Services\HelpFunctions;

class TestController extends Controller {
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	public function getModel($uuid)
	{
		$model = HelpFunctions::getEntityByUuid(Certificate::class, $uuid);
		
		dump($model->position);
	}

	public function parseFile()
	{
		$filaPath = $_SERVER['DOCUMENT_ROOT'] . '/../storage/app/private/attachments/2022-05-25.13.1.txt';
		$attachmentContent = file_get_contents($filaPath);
		//\Log::debug($attachmentContent);

		$inAirStr = HelpFunctions::mailGetStringBetween($attachmentContent, 'X-Plane', 'X-Plane');
		$inAirArr = explode("\n", trim($inAirStr));
		//\Log::debug($inAirArr);
		foreach ($inAirArr as $item) {
			//\Log::debug($item);
			$itemData = explode(' ', preg_replace('| +|', ' ', $item));
			//\Log::debug($itemData);
			/*if ($itemData[3] == 'IN-AIR') {
				$platformLog = new PlatformLog();
				$platformLog->platform_data_id = 1;
				$platformLog->action_type = PlatformLog::IN_AIR_ACTION_TYPE;
				$platformLog->start_at = trim($itemData[0]);
				$platformLog->stop_at = trim($itemData[2]);
				$platformLog->duration = trim($itemData[4]);
				$platformLog->save();
			}*/
		}
	}
}