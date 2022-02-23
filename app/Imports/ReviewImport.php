<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Content;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Row;
use Throwable;

class ReviewImport implements OnEachRow, WithProgressBar
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

			$city = HelpFunctions::getEntityByAlias(City::class, trim($row[1]));
			$cityId = $city ? $city->id : 0;

			$parent = HelpFunctions::getEntityByAlias(Content::class, 'reviews');
			$parentId = $parent ? $parent->id : 0;

			$content = new Content();
			$content->title = trim($row[0]);
			$content->alias = (string)\Webpatser\Uuid\Uuid::generate();
			$content->preview_text = trim($row[2]);
			$content->detail_text = trim($row[3]);
			$content->parent_id = $parentId;
			$content->city_id = $cityId;
			$content->created_at = $row[4] ? Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])) : Carbon::now();
			$content->updated_at = $row[5] ? Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5])) : Carbon::now();
			$content->published_at = $row[4] ? Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])) : Carbon::now();
			$content->meta_title = 'Отзыв от клиента ' . trim($row[0]) . ($city ? ' из города ' . $city->name : '') . ' от ' . Carbon::parse($content->created_at)->format('d.m.Y');
			$content->meta_description = 'Отзыв от клиента ' . trim($row[0]) . ($city ? ' из города ' . $city->name : '') . ' от ' . Carbon::parse($content->created_at)->format('d.m.Y');
			$content->save();

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			\Log::debug('500 - ' . $e->getMessage() . ' - ' . implode(' | ', $row));
		}
    }
}
