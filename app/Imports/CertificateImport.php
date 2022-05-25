<?php

namespace App\Imports;

use App\Models\Certificate;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Row;
use Throwable;

class CertificateImport implements OnEachRow, WithProgressBar
{
	use Importable;

	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
		$rowIndex = $row->getIndex();
		$row = $row->toArray();

		if ($rowIndex == 1) return null;

		try {
			\DB::beginTransaction();
			
			$data = [
				'sell_date' => trim($row[1]),
				'duration' => trim($row[2]),
				'amount' => trim($row[3]),
				'location' => trim($row[4]),
				'payment_method' => trim($row[5]),
				'status' => trim($row[6]),
				'comment' => strip_tags(trim($row[7])),
			];
			
			\Log::debug($data);

			$certificate = new Certificate();
			$certificate->expire_at = (trim($row[8]) == 'Бессрочный') ? null : Carbon::parse($row[8])->format('Y-m-d H:i:s');
			$certificate->data_json = $data;
			$certificate->save();

			$certificate->number = trim($row[0]);
			$certificate->save();
			\Log::debug($certificate->id);

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			\Log::debug('500 - ' . $e->getMessage() . ' - ' . implode(' | ', $row));
		}
    }
}
