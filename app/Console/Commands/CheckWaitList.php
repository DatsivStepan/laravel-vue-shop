<?php

namespace App\Console\Commands;

use App\Services\ProductWaitListService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckWaitList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waitlist:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wait list check';
    /**
     * @var ProductWaitListService
     */
    private $productWaitListService;

    /**
     * Create a new command instance.
     *
     * @param ProductWaitListService $productWaitListService
     */
    public function __construct(ProductWaitListService $productWaitListService)
    {
        parent::__construct();
        $this->productWaitListService = $productWaitListService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Waitlist processing started…');

        $tenants = tenancy()->query()->get();

        foreach ($tenants as $tenant) {
            setTenant($tenant->id);

            Log::info("Tenant $tenant->id:");

            $this->productWaitListService->deleteInactive();

            $sumSeats = $this->productWaitListService->checkingAndSendingNotificationUsersAboutAvailableProducts();

            Log::info(" - waitlist $sumSeats records sent");
        }

        tenancy()->end();

        Log::info('Waitlist processing ended.');
    }
}
