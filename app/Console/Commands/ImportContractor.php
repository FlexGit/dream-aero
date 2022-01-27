<?php

namespace App\Console\Commands;

use App\Imports\ContractorImport;
use Illuminate\Console\Command;

class ImportContractor extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'contractor:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import contractor from old CRM';

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
		(new ContractorImport)->withOutput($this->output)->import(storage_path('app/public/modx_crmcont2.xlsx'));
		$this->output->success('Import successful');
	}
}
