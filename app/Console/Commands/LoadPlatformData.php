<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\PlatformData;
use App\Models\PlatformLog;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class LoadPlatformData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform_data:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load platform data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$locations = Location::get();
		
		/** @var \Webklex\PHPIMAP\Client $client */
		$client = Client::account('default');
		$client->connect();
	
		/** @var \Webklex\PHPIMAP\Client $client */
		/** @var \Webklex\PHPIMAP\Folder $folder */
		$folder = $client->getFolderByName(env('IMAP_DEFAULT_FOLDER'));
	
		/** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
		//$messages = $folder->messages()->unseen()->get();

		/** @var \Webklex\PHPIMAP\Folder $folder */
		/** @var \Webklex\PHPIMAP\Query\WhereQuery $query */
		$query = $folder->query();
		
		\Log::debug(Carbon::now()->subDay());
		
		/** @var \Webklex\PHPIMAP\Query\WhereQuery $query */
		/** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
		$messages = $query->/*unseen()->*/since(/*Carbon::now()->subDay()*/'22.06.2022')->get();
	
		\Log::debug('messages = ' . count($messages));
		
		/** @var \Webklex\PHPIMAP\Message $message */
		foreach ($messages as $message) {
			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var \Webklex\PHPIMAP\Attribute $subject */
			$subject = $message->getSubject();
			\Log::debug($subject);

			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var string|null $body */
			$body = $message->getTextBody();
			//\Log::debug($body);

			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var string $raw */
			//$raw = $message->getRawBody();
			//\Log::debug($raw);
			
			$dataAt = HelpFunctions::mailGetStringBefore($body, 'System Total Tota', 13);
			//\Log::debug('dataAt = ' . $dataAt);
			$dataAt = preg_replace('/[^\d-]/', '', $dataAt);
			\Log::debug('dataAt = ' . $dataAt);
			if (!$dataAt) return 0;

			$totalUp = HelpFunctions::mailGetStringBetween($body, 'Platform Total UP', 'InAirNoMotion Total Total');
			//\Log::debug('totalUp = ' . $totalUp);
			$inAirNoMotion = HelpFunctions::mailGetStringBetween($body, 'InAirNoMotion Total IANM', '');
			//\Log::debug('inAirNoMotion = ' . $inAirNoMotion);
			
			$locationId = $simulatorId = 0;
			$letterNames = [];
			foreach ($locations as $location) {
				foreach ($location->simulators as $simulator) {
					$data = json_decode($simulator->pivot->data_json, true);
					$letterNames[$location->id . '_' . $simulator->id] = isset($data['letter_name']) ? $data['letter_name'] : '';
				}
				
				//\Log::debug($letterNames);
				
				foreach ($letterNames as $locationSimulatorId => $letterName) {
					//\Log::debug($letterName . ' - ' . $subject[0]);
					if ($letterName != $subject[0]) continue;
					
					$locationSimulatorArr = explode('_', $locationSimulatorId);
					$locationId = $locationSimulatorArr[0];
					$simulatorId = $locationSimulatorArr[1];
				}
			}
			//\Log::debug($locationId . ' - ' . $simulatorId);
			if (!$locationId || !$simulatorId) return 0;
			
			$platformDataExists = false;
			$platformData = PlatformData::where('location_id', $locationId)
				->where('flight_simulator_id', $simulatorId)
				->where('data_at', $dataAt)
				->first();
			if (!$platformData) {
				$platformData = new PlatformData();
				$platformData->location_id = $locationId;
				$platformData->flight_simulator_id = $simulatorId;
				$platformData->data_at = Carbon::parse($dataAt)->format('Y-m-d');
			} else {
				$platformDataExists = true;
			}
			$platformData->total_up = Carbon::parse($totalUp)->format('H:i:s');
			$platformData->in_air_no_motion = Carbon::parse($inAirNoMotion)->format('H:i:s');
			if (!$platformData->save()) return 0;
			
			//\Log::debug($platformData->toArray());
			
			if ($platformDataExists) {
				$platformLogsDeleted = PlatformLog::where('platform_data_id', $platformData->id)
					->delete();
			}
			
			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var \Webklex\PHPIMAP\Support\AttachmentCollection $attachments */
			$attachments = $message->getAttachments();
			foreach ($attachments as $attachment) {
				$attachmentPath = './storage/app/private/attachments/';
				$attachmentName = $dataAt . '.' . $locationId . '.' . $simulatorId . '.txt';

				/** @var \Webklex\PHPIMAP\Attachment $attachment */
				/** @var boolean $status */
				$status = $attachment->save($attachmentPath, $attachmentName);
				//\Log::debug('status = ' . $status);
				if (!$status) continue;

				$attachmentContent = file_get_contents($attachmentPath . $attachmentName);
				//\Log::debug($attachmentContent);

				$inAirStr = HelpFunctions::mailGetStringBetween($attachmentContent, 'X-Plane', 'X-Plane');
				$inAirArr = explode("\n", trim($inAirStr));
				//\Log::debug($inAirArr);
				foreach ($inAirArr as $item) {
					//\Log::debug($item);
					$itemData = explode(' ', preg_replace('| +|', ' ', $item));
					//\Log::debug($itemData);
					if (!isset($itemData[3])) continue;

					if ($itemData[3] == 'IN-AIR') {
						$platformLog = new PlatformLog();
						$platformLog->platform_data_id = $platformData->id;
						$platformLog->action_type = PlatformLog::IN_AIR_ACTION_TYPE;
						$platformLog->start_at = trim($itemData[0]);
						$platformLog->stop_at = trim($itemData[2]);
						$platformLog->duration = trim($itemData[4]);
						$platformLog->save();
					}
				}

				$inUpStr = HelpFunctions::mailGetStringBetween($attachmentContent, 'Platform', 'Platform');
				$inUpArr = explode("\n", trim($inUpStr));
				foreach ($inUpArr as $item) {
					$itemData = explode(' ', preg_replace('| +|', ' ', $item));
					if (!isset($itemData[3])) continue;
					if ($itemData[3] == 'UP') {
						$platformLog = new PlatformLog();
						$platformLog->platform_data_id = $platformData->id;
						$platformLog->action_type = PlatformLog::IN_UP_ACTION_TYPE;
						$platformLog->start_at = trim($itemData[0]);
						$platformLog->stop_at = trim($itemData[2]);
						$platformLog->duration = trim($itemData[4]);
						$platformLog->save();
					}
				}

				if (HelpFunctions::mailGetTimeSeconds($inAirNoMotion) >= 600) {
					$ianmTime = HelpFunctions::mailGetStringBetween($attachmentContent, 'InAirNoMotion', 'InAirNoMotion Total Total');
					$ianmStr = explode("\n", trim($ianmTime));
					foreach ($ianmStr as $item) {
						$itemData = explode(' ', $item);

						$platformLog = new PlatformLog();
						$platformLog->platform_data_id = $platformData->id;
						$platformLog->action_type = PlatformLog::IANM_ACTION_TYPE;
						$platformLog->start_at = trim($itemData[0]);
						$platformLog->stop_at = trim($itemData[2]);
						$platformLog->duration = trim($itemData[4]);
						$platformLog->save();
					}
				}
			}
			
			/** @var \Webklex\PHPIMAP\Message $message */
			$message->setFlag('Seen');
		}
	
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - platform_data:load - OK');
    	
        return 0;
    }
}
