<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PlatformDataReportExport implements WithMultipleSheets
{
	use Exportable;
	
	private $data;
	private $periods;

	public function __construct(array $data, array $periods)
	{
		$this->data = $data;
		$this->periods = $periods;
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
		foreach ($this->periods as $period) {
			$sheets[] = new PlatformDataPeriodReportExport($this->data, $period);
		}
		
		return $sheets;
	}
}
