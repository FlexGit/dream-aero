<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\LockingPeriod;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnlockPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'period:unlock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock locking period while booking deal is creating';

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
    	LockingPeriod::where('created_at', '<=', Carbon::now()->subMinutes(Event::LOCKING_PERIOD))
			->delete();
			
        return 0;
    }
}
