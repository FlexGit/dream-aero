<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class AeroflotWriteOffReportExport extends DefaultValueBinder  implements FromView, ShouldAutoSize, WithCustomValueBinder
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
		/*if (in_array($cell->getColumn(), ['G','H','I']) && is_int($value)) {
			$cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
			
			return true;
		}*/

		// else return default behavior
		return parent::bindValue($cell, $value);
	}
}
