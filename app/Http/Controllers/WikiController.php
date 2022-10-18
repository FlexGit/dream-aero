<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\HelpFunctions;

class WikiController extends Controller
{
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$parentContent = HelpFunctions::getEntityByAlias(Content::class, Content::WIKI_TYPE);
		
		$content = Content::where('parent_id', $parentContent->id)
			->first();
		
		return view('admin.wiki.index', [
			'content' => $content,
			'type' => Content::WIKI_TYPE,
		]);
	}
}