<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\DefaultValueBinder;

class AeroflotAccrualReportExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithColumnFormatting
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}
	
	public function view(): View
	{
		return view('admin.report.aeroflot.accrual.list', $this->data);
	}
	
	public function array(): array
	{
		return $this->data;
	}
	
	public function columnFormats(): array
	{
		return [
			'E' => NumberFormat::FORMAT_NUMBER,
			'F' => NumberFormat::FORMAT_NUMBER,
		];
	}
}
