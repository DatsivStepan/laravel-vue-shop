<?php

namespace App\Console\Commands;

use App\Repositories\Admin\ScheduledEnrollmentsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduledEnrollments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enroll:schuduled_enroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled Enrollments';

    /**
     * @var ScheduledEnrollmentsRepository
     */
    private $scheduledEnrollmentsRepository;

    /**
     * Create a new command instance.
     *
     * @param ScheduledEnrollmentsRepository $scheduledEnrollmentsRepository
     */
    public function __construct(ScheduledEnrollmentsRepository $scheduledEnrollmentsRepository)
    {
        parent::__construct();
        $this->scheduledEnrollmentsRepository = $scheduledEnrollmentsRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Scheduled Enrollments start');

        $tenants = tenancy()->query()->get();

        foreach ($tenants as $tenant) {
            setTenant($tenant->id);

            Log::info("Tenant $tenant->id:");
            $sumEnrolled = 0;

            if ($productsOrder = $this->scheduledEnrollmentsRepository->getPastNotEnrolled()) {
                foreach ($productsOrder as $productOrder) {
                    $this->scheduledEnrollmentsRepository->enroll($productOrder->id);
                    $sumEnrolled++;
                }
            }

            Log::info(" - enroled $sumEnrolled ");
        }

        tenancy()->end();

        Log::info('Scheduled Enrollments end');
    }
}
