<?php

namespace App\Console\Commands;

use App\Imports\CertificateImport;
use Illuminate\Console\Command;

class ImportCertificate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'certificate:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import certificates from old CRM';

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
		(new CertificateImport)->withOutput($this->output)->import(storage_path('app/public/certificates2.xlsx'));
		$this->output->success('Import successful');
	}
}
