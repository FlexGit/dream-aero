<?php

namespace App\Imports;

use App\Models\Certificate;
use App\Models\Content;
use App\Services\HelpFunctions;
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

			$cityId = 0;

			$parent = HelpFunctions::getEntityByAlias(Content::class, 'news');
			$parentId = $parent ? $parent->id : 0;

			$certificate = new Certificate();
			$certificate->number = trim($row[0]);
			$content->alias = trim($row[2]);
			$content->preview_text = trim($row[3]);
			$content->detail_text = trim($row[4]);
			$content->parent_id = $parentId;
			$content->city_id = $cityId;
			$content->created_at = $row[5] ? Carbon::createFromTimestamp($row[5])->format('Y-m-d H:i:s') : Carbon::now();
			$content->updated_at = $row[6] ? Carbon::createFromTimestamp($row[6])->format('Y-m-d H:i:s') : Carbon::now();
			$content->published_at = $row[7] ? Carbon::createFromTimestamp($row[7])->format('Y-m-d H:i:s') : Carbon::now();
			$content->meta_title = trim($row[0]);
			$content->meta_description = trim($row[1]);
			$content->save();

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			\Log::debug('500 - ' . $e->getMessage() . ' - ' . implode(' | ', $row));
		}
    }
}
