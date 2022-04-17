<?php

namespace App\Console\Commands;

use App\Imports\RatingImport;
use Illuminate\Console\Command;

class ImportRating extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'rating:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import rating from old CRM';

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
		(new RatingImport)->withOutput($this->output)->import(storage_path('app/public/modx_rating.xlsx'));
		$this->output->success('Import successful');
	}
}
