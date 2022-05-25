<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\PlatformData;
use App\Models\PlatformLog;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;
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
		
		/** @var \Webklex\PHPIMAP\Query\WhereQuery $query */
		/** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
		$messages = $query->since('25.05.2022')->get();
	
		/** @var \Webklex\PHPIMAP\Message $message */
		foreach ($messages as $message) {
			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var \Webklex\PHPIMAP\Attribute $subject */
			$subject = $message->getSubject();
			\Log::debug($subject);
			
			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var string|null $body */
			$body = $message->getTextBody();
			\Log::debug($body);
			
			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var string $raw */
			//$raw = $message->getRawBody();
			//\Log::debug($raw);
			
			$dataAt = HelpFunctions::mailGetStringBefore($body, 'System Total Tota', 13);
			\Log::debug('dataAt = ' . $dataAt);
			$dataAt = preg_replace('/[^\d-]/', '', $dataAt);
			\Log::debug('dataAt = ' . $dataAt);
			if (!$dataAt) return 0;

			$totalUp = HelpFunctions::mailGetStringBetween($body, 'Platform Total UP', 'InAirNoMotion Total Total');
			\Log::debug('totalUp = ' . $totalUp);
			$inAirNoMotion = HelpFunctions::mailGetStringBetween($body, 'InAirNoMotion Total IANM', '');
			\Log::debug('inAirNoMotion = ' . $inAirNoMotion);
			
			$locationId = $simulatorId = 0;
			$letterNames = [];
			foreach ($locations as $location) {
				foreach ($location->simulators as $simulator) {
					$data = json_decode($simulator->pivot->data_json, true);
					$letterNames[$location->id][$simulator->id] = isset($data['letter_name']) ? $data['letter_name'] : '';
				}
				
				//\Log::debug($letterNames);
				
				foreach ($letterNames as $locationId) {
					foreach ($locationId as $simulatorId => $letterName) {
						\Log::debug($letterName . ' - ' . $subject[0]);
						if ($letterName != $subject[0]) continue;
					}
				}
			}
			\Log::debug($locationId . ' - ' . $simulatorId);
			if (!$locationId || !$simulatorId) return 0;
			
			$platformData = PlatformData::where('location_id', $locationId)
				->where('location_id', $simulatorId)
				->where('data_at', $dataAt)
				->first();
			if (!$platformData) {
				$platformData = new PlatformData();
				$platformData->location_id = $locationId;
				$platformData->flight_simulator_id = $simulatorId;
				$platformData->data_at = Carbon::parse($dataAt)->format('Y-m-d');
			}
			$platformData->total_up = $totalUp;
			$platformData->in_air_no_motion = $inAirNoMotion;
			if (!$platformData->save()) return 0;
			
			/** @var \Webklex\PHPIMAP\Message $message */
			/** @var \Webklex\PHPIMAP\Support\AttachmentCollection $attachments */
			$attachments = $message->getAttachments();
			foreach ($attachments as $attachment) {
				/** @var \Webklex\PHPIMAP\Attachment $attachment */
				/** @var boolean $status */
				$status = $attachment->save('./storage/app/private/attachments/', null);
				\Log::debug('status = ' . $status);
			}
			
			/** @var \Webklex\PHPIMAP\Message $message */
			$message->unsetFlag('Seen');
			
			break;
		}
	
		//$connection = imap_open(env('PLATFORM_IMAP'), env('PLATFORM_MAIL_LOGIN'), env('PLATFORM_MAIL_PASSWORD'));
		//if(!$connection) return 0;

		/*$mails = imap_search($connection,'UNSEEN');
		if ($mails === false) return 0;
	
		$mailData = [];
		$i = 1;
		foreach ($mails as $mailId) {
			$msgHeader = imap_headerinfo($connection, intval($mailId));
			$mailData[$i]['time'] = time($msgHeader->MailDate);
			$mailData[$i]['date'] = $msgHeader->MailDate;
			
			foreach ($msgHeader->from as $fromData) {
				$mailData[$i]["from"] = $fromData->mailbox . '@' . $fromData->host;
			}
			if ($mailData[$i]["from"] != 'exmonsrv@extrino.net') break;
			
			$msgStructure = imap_fetchstructure($connection, $mailId);
			$msgBody = imap_fetchbody($connection, $mailId, 1);
			$recursiveData = HelpFunctions::mailSearch($msgStructure);
			$body = HelpFunctions::mailStructureEncoding($recursiveData['encoding'], $msgBody);
			
			if (!HelpFunctions::mailCheckUtf8($recursiveData['charset'])) {
				$body = HelpFunctions::mailConvertToUtf8($recursiveData['charset'], $msgBody);
			}
			
			$mailsBody = quoted_printable_decode($body);
			
			$dataAt = HelpFunctions::mailGetStringBefore($mailsBody, 'System Total Tota', 13);
			$dataAt = preg_replace('/[^\d-]/', '', $dataAt);
			
			$totalUp = HelpFunctions::mailGetStringBetween($mailsBody, 'Platform Total UP', 'InAirNoMotion Total Total');
			$inAirNoMotion = HelpFunctions::mailGetStringBetween($mailsBody, 'InAirNoMotion Total IANM', '');
			
			if (!$dataAt) return 0;
			
			$txtFile = '';
			if (isset($msgStructure->parts)) {
				for ($j = 1, $f = 2; $j <= count($msgStructure->parts); $j++, $f++) {
					if (in_array($msgStructure->parts[$j]->subtype, ['OCTET-STREAM'])) {
						$mailData[$i]['attachs'][$j]['type'] = $msgStructure->parts[$j]->subtype;
						$mailData[$i]['attachs'][$j]['size'] = $msgStructure->parts[$j]->bytes;
						$mailData[$i]['attachs'][$j]['name'] = HelpFunctions::mailGetImapTitle($msgStructure->parts[$j]->parameters[0]->value);
						$mailData[$i]['attachs'][$j]['file'] = HelpFunctions::mailStructureEncoding($msgStructure->parts[$j]->encoding, imap_fetchbody($connection, intval($mailId), $f));
						$txtFile = $mailData[$i]['attachs'][$j]['file'];
					}
				}
			}
			
			$locationId = $simulatorId = 0;
			foreach ($locations as $location) {
				if (!$location->pivot) continue;
				
				$locationSimulatorData = $location->pivot->data_json;
				$letterName = isset($locationSimulatorData['letter_name']) ? $locationSimulatorData['letter_name'] : '';
				$letterNamePos = strpos($mailsBody, $letterName);
				if ($letterNamePos !== false) {
					$locationId = $location->id;
					$simulatorId = $location->pivot->flight_simulator_id;
					break;
				}
			}
			if (!$locationId || !$simulatorId) return 0;
			
			$platformData = PlatformData::where('location_id', $locationId)
				->where('location_id', $simulatorId)
				->where('data_at', $dataAt)
				->first();
			if (!$platformData) {
				$platformData = new PlatformData();
				$platformData->location_id = $locationId;
				$platformData->flight_simulator_id = $simulatorId;
				$platformData->data_at = Carbon::parse($dataAt)->format('Y-m-d');
			}
			$platformData->total_up = $totalUp;
			$platformData->in_air_no_motion = $inAirNoMotion;
			if (!$platformData->save()) return 0;
			
			if ($txtFile) {
				$inAirStr = HelpFunctions::mailGetStringBetween($txtFile, 'X-Plane', 'X-Plane');
				$inAirArr = explode('\n', trim($inAirStr));
				foreach ($inAirArr as $item) {
					$itemData = explode(' ', preg_replace('| +|', ' ', $item));
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
				
				$inUpStr = HelpFunctions::mailGetStringBetween($txtFile, 'Platform', 'Platform');
				$inUpArr = explode('\n', trim($inUpStr));
				foreach ($inUpArr as $item) {
					$itemData = explode(' ', preg_replace('| +|', ' ', $item));
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
					$ianmTime = HelpFunctions::mailGetStringBetween($txtFile, 'InAirNoMotion', 'InAirNoMotion Total Total');
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
		}*/

		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - platform_data:load - OK');
    	
        return 0;
    }
}
