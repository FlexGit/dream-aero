<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromArray;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PlatformDataPeriodReportExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder, WithTitle
{
	use Exportable;
	
	private $data;
	private $period;

	public function __construct($data, $period)
	{
		$this->data = $data;
		$this->period = $period;
	}
	
	/**
	 * @return View
	 */
	public function view(): View
	{
		$this->data['period'] = $this->period;
		
		return view('admin.report.platform.export', $this->data);
	}
	
	/**
	 * @return array
	 */
	public function array(): array
	{
		return $this->data;
	}
	
	/**
	 * @return string
	 */
	public function title(): string
	{
		return $this->period;
	}
}
