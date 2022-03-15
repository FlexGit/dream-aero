<?php

namespace App\Console\Commands;

use App\Imports\ContractorImport;
use App\Imports\FlightImport;
use Illuminate\Console\Command;

class ImportFlight extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'flight:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import flight and deal from old CRM';

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
		(new FlightImport)->withOutput($this->output)->import(storage_path('app/public/modx_flights1.xlsx'));
		$this->output->success('Import successful');
	}
}
