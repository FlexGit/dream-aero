<?php

namespace App\Console\Commands\Roistat;

use App\Services\RoistatService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RoistatSetStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roistat:set_statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Roistat: Set Statuses';

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
		$roistat = new RoistatService();
		$roistat->setStatuses();
    
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - roistat:set_statuses - OK');
    	
        return 0;
    }
}
