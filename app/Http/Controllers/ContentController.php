<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Content;
use App\Services\HelpFunctions;
use Illuminate\Http\Request;
use Validator;

class ContentController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}

	/**
	 * @param $version
	 * @param $type
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index($version, $type)
	{
		return view('admin.content.index', [
			'version' => $version,
			'type' => $type,
		]);
	}

	/**
	 * @param $version
	 * @param $type
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax($version, $type)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}
		
		$id = $this->request->id ?? 0;

		$contents = Content::orderBy('created_at', 'desc')
			->where('version', $version)
			->where('parent_id', $parentContent->id);
		if ($this->request->search_content) {
			$contents = $contents->where(function ($query) {
				$query->where('title', 'like', '%' . $this->request->search_content . '%')
					->orWhere('detail_text', 'like', '%' . $this->request->search_content . '%')
					->orWhere('preview_text', 'like', '%' . $this->request->search_content . '%')
					->orWhere('alias', 'like', '%' . $this->request->search_content . '%')
					->orWhereHas('city', function ($q) {
						return $q->where('cities.name', 'like', '%' . $this->request->search_content . '%');
					})
				;
			});
		}
		if ($id) {
			$contents = $contents->where('id', '<', $id);
		}
		$contents = $contents->limit(20)->get();

		$VIEW = view('admin.content.list', [
			'contents' => $contents,
			'version' => $version,
			'type' => $type,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $version
	 * @param $type
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($version, $type, $id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}

		$content = Content::where('version', $version)
			->where('parent_id', $parentContent->id)
			->find($id);
		if (!$content) return response()->json(['status' => 'error', 'reason' => 'Материал не найден']);

		$cities = City::where('version', $version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.content.modal.edit', [
			'content' => $content,
			'version' => $version,
			'type' => $type,
			'cities' => $cities,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $version
	 * @param $type
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add($version, $type)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}

		$cities = City::where('version', $version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.content.modal.add', [
			'version' => $version,
			'type' => $type,
			'cities' => $cities,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $version
	 * @param $type
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function confirm($version, $type, $id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}

		$content = Content::where('version', $version)
			->where('parent_id', $parentContent->id)
			->find($id);
		if (!$content) return response()->json(['status' => 'error', 'reason' => 'Материал не найден']);

		$VIEW = view('admin.content.modal.delete', [
			'content' => $content,
			'version' => $version,
			'type' => $type,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $version
	 * @param $type
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store($version, $type)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'title' => ['required', 'min:3', 'max:250'],
			'alias' => ['required', 'min:3', 'max:250', 'regex:/([A-Za-z0-9\-]+)/', 'unique:contents'],
			'published_at' => ['date'],
			'photo_preview_file' => ['sometimes', 'image', 'max:5120', 'mimes:webp,png,jpg,jpeg'],
			'city_id' => ['sometimes', 'numeric', 'min:0', 'not_in:0', 'valid_city'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'title' => 'Заголовок',
				'alias' => 'Алиас',
				'published_at' => 'Дата публикации',
				'photo_preview_file' => 'Фото-превью',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}
		
		$isFileUploaded = false;
		if($file = $this->request->file('photo_preview_file')) {
			$isFileUploaded = $file->move(public_path('upload/content/' . $version . '/' . $type), $file->getClientOriginalName());
		}
		
		$data = [
			'photo_preview_file_path' => $isFileUploaded ? 'content/' . $version . '/' . $type . '/' . $file->getClientOriginalName() : '',
			'video_url' => $this->request->video_url ?? '',
		];
		
		$content = new Content();
		$content->title = $this->request->title;
		$content->alias = $this->request->alias;
		$content->preview_text = $this->request->preview_text;
		$content->detail_text = $this->request->detail_text;
		$content->parent_id = $parentContent->id;
		$content->city_id = $this->request->city_id;
		$content->version = $version;
		$content->meta_title = $this->request->meta_title;
		$content->meta_description = $this->request->meta_description;
		$content->is_active = (bool)$this->request->is_active;
		$content->data_json = $data;
		$content->published_at = $this->request->published_at;
		if (!$content->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $version
	 * @param $type
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($version, $type, $id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}

		$content = Content::where('parent_id', $parentContent->id)
			->find($id);
		if (!$content) return response()->json(['status' => 'error', 'reason' => 'Материал не найден']);

		$rules = [
			'title' => ['required', 'min:3', 'max:250'],
			'alias' => ['required', 'min:3', 'max:250', 'regex:/([A-Za-z0-9\-]+)/', 'unique:contents,alias,' . $id],
			'published_at' => ['date'],
			'photo_preview_file' => ['sometimes', 'image', 'max:5120', 'mimes:webp,png,jpg,jpeg'],
			'city_id' => ['sometimes', 'numeric', 'min:0', 'not_in:0', 'valid_city'],
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'title' => 'Заголовок',
				'alias' => 'Алиас',
				'published_at' => 'Дата публикации',
				'photo_preview_file' => 'Фото-превью',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$isFileUploaded = false;
		if($file = $this->request->file('photo_preview_file')) {
			$isFileUploaded = $file->move(public_path('upload/content/' . $version . '/' . $type), $file->getClientOriginalName());
		}
		
		$data = [
			'photo_preview_file_path' => $isFileUploaded ? 'content/' . $version . '/' . $type . '/' . $file->getClientOriginalName() : '',
			'video_url' => $this->request->video_url ?? '',
		];

		$content->title = $this->request->title;
		$content->alias = $this->request->alias;
		$content->preview_text = $this->request->preview_text;
		$content->detail_text = $this->request->detail_text;
		$content->parent_id = $parentContent->id;
		$content->city_id = $this->request->city_id;
		$content->version = $version;
		$content->meta_title = $this->request->meta_title;
		$content->meta_description = $this->request->meta_description;
		$content->is_active = (bool)$this->request->is_active;
		$content->data_json = $data;
		$content->published_at = $this->request->published_at;
		if (!$content->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $version
	 * @param $type
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($version, $type, $id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}

		$content = Content::where('version', $version)
			->where('parent_id', $parentContent->id)
			->find($id);
		if (!$content) return response()->json(['status' => 'error', 'reason' => 'Материал не найден']);

		if (!$content->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $version
	 * @param $type
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function imageUpload($version, $type) {
		$parentContent = HelpFunctions::getEntityByAlias(Content::class, $type);
		if (!$parentContent) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}

		$file = $this->request->file('file');
		if (!$file->move(public_path('/upload/content/' . $version . '/' . $type . '/'), $file->getClientOriginalName())) {
			return response()->json(['status' => 'error', 'reason' => 'Не удалось загрузить файл']);
		}

		return response()->json([
			'location' => url('/upload/content/' . $version . '/' . $type . '/' . $file->getClientOriginalName()),
		]);
	}
}
