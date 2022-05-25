<?php

namespace App\Exports;

/*use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;*/

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NpsReportExport implements FromView/*, WithColumnFormatting*/
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}
	
	public function view(): View
	{
		return view('admin.report.nps.export', $this->data);
	}
	
	/*public function array(): array
	{
		return $this->data;
	}
	
	public function columnFormats(): array
	{
		return [
			'E' => NumberFormat::FORMAT_NUMBER,
			'F' => NumberFormat::FORMAT_NUMBER,
			'G' => NumberFormat::FORMAT_NUMBER,
		];
	}*/
}
