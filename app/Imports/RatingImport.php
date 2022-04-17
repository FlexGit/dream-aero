<?php

namespace App\Imports;

use App\Models\Content;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Row;
use Throwable;

class RatingImport implements OnEachRow, WithProgressBar
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

		//\DB::connection()->enableQueryLog();
		
		$content = Content::where('alias', trim($row[0]))
			->where('parent_id', 1)
			->first();
		if (!$content) return null;
		
		$content->rating_value = trim($row[1]);
		$content->rating_count = trim($row[2]);
		$content->rating_ips = trim($row[3]);
		$content->save();
		
		//\Log::debug(\DB::getQueryLog());
    }
}
