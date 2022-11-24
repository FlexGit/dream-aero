<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\DealPosition;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SetBillPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill_positions:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Bill Positions';

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
    	$bills = Bill::where('deal_position_id', '>', 0)
			->get();
    	/** @var Bill[] $bills */
		foreach ($bills as $bill) {
			$position = DealPosition::find($bill->deal_position_id);
			if (!$position) continue;
		
			try {
				$bill->positions()->sync((array)$position->id);
			} catch (Throwable $e) {
				\Log::debug('500 - ' . get_class($this) . ': ' . $e->getMessage());
			
				return 0;
			}
		}
	
		$this->info(Carbon::now()->format('Y-m-d H:i:s') . ' - ' . get_class($this) . ': OK');
    	
        return 0;
    }
}
