<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class AeroflotWriteOffReportExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithColumnFormatting
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
	
	public function bindValue(Cell $cell, $value)
	{
		if (in_array($cell->getColumn(), ['D'])) {
			$cell->setValueExplicit($value, DataType::TYPE_STRING);
			
			return true;
		}
		
		// else return default behavior
		return parent::bindValue($cell, $value);
	}
	
	public function columnFormats(): array
	{
		return [
			'D' => NumberFormat::FORMAT_TEXT,
			'G' => NumberFormat::FORMAT_NUMBER,
			'H' => NumberFormat::FORMAT_NUMBER,
			'I' => NumberFormat::FORMAT_NUMBER,
		];
	}
	
}
