<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FlightLogMultipleSheetsReportExport implements WithMultipleSheets
{
	use Exportable;
	
	private $data;
	private $cities;

	public function __construct($data, $cities)
	{
		$this->data = $data;
		$this->cities = $cities;
	}
	
	/**
	 * @return array
	 */
	public function array(): array
	{
		return $this->data;
	}
	
	/**
	 * @return array
	 */
	public function sheets(): array
	{
		$sheets = [];
		foreach ($this->cities as $city) {
			foreach($city->locations as $location) {
				foreach ($location->simulators as $simulator) {
					$sheets[] = new FlightLogLocationReportExport($this->data, $location, $simulator);
				}
			}
		}
		
		return $sheets;
	}
}
