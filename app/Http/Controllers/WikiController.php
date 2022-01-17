<?php

namespace App\Http\Controllers;

class WikiController extends Controller
{
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		return view('admin.wiki.index');
	}
}