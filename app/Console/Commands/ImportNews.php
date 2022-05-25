<?php

namespace App\Console\Commands;

use App\Imports\NewsImport;
use App\Imports\ReviewImport;
use Illuminate\Console\Command;

class ImportNews extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'news:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import news from old CRM';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$this->output->title('Starting import');
		(new NewsImport)->withOutput($this->output)->import(storage_path('app/public/modx_news.xlsx'));
		$this->output->success('Import successful');
	}
}
