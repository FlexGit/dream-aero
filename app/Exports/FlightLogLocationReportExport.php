<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class FlightLogLocationReportExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder, WithTitle
{
	use Exportable;
	
	private $data;
	private $location;
	private $simulator;

	public function __construct($data, $location, $simulator)
	{
		$this->data = $data;
		$this->location = $location;
		$this->simulator = $simulator;
	}
	
	/**
	 * @return View
	 */
	public function view(): View
	{
		$this->data['location'] = $this->location;
		$this->data['simulator'] = $this->simulator;
		
		return view('admin.report.flight-log.export', $this->data);
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
		return $this->location->name . ' ' . $this->simulator->alias;
	}
	
	/**
	 * @param Cell $cell
	 * @param mixed $value
	 * @return bool
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function bindValue(Cell $cell, $value)
	{
		if (is_numeric($value)) {
			$cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
			
			return true;
		}
		
		// else return default behavior
		return parent::bindValue($cell, $value);
	}
}
