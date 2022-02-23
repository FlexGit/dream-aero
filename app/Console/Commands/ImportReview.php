<?php

namespace App\Console\Commands;

use App\Imports\ReviewImport;
use Illuminate\Console\Command;

class ImportReview extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'review:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import review from old CRM';

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
		(new ReviewImport)->withOutput($this->output)->import(storage_path('app/public/review.xlsx'));
		$this->output->success('Import successful');
	}
}
