<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AeroflotWriteOffReportExport implements FromView, WithColumnFormatting, ShouldAutoSize
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}
	
	public function view(): View
	{
		return view('admin.report.aeroflot.write-off.list', $this->data);
	}
	
	public function array(): array
	{
		return $this->data;
	}
	
	public function columnFormats(): array
	{
		return [
			'D' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
			'G' => NumberFormat::FORMAT_NUMBER,
			'H' => NumberFormat::FORMAT_NUMBER,
			'I' => NumberFormat::FORMAT_NUMBER,
		];
	}
}
