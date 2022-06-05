<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AeroflotWriteOffReportExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromView, WithColumnFormatting, ShouldAutoSize, WithCustomValueBinder
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
		];
	}
}
