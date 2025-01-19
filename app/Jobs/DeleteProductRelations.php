<?php

namespace App\Jobs;

use App\Models\VendorsAssings;
use App\Repositories\Admin\ProductCategoriesRepository;
use App\Repositories\Admin\ProductCoursesRepository;
use App\Repositories\Admin\ProductInstructorsRepository;
use App\Repositories\Admin\VendorsAssignRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteProductRelations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $productId = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $options = [
            'filters' => ['product_id' => $this->productId]
        ];
        (new ProductCategoriesRepository())->deleteAllItems($options);

        (new ProductInstructorsRepository())->deleteAllItems($options);

        (new ProductCoursesRepository())->deleteAllItems($options);

        $options = [
            'filters' => [
                'instance_id' => $this->productId,
                'instance_type' => VendorsAssings::INSTANCE_TYPES_PRODUCT
            ]
        ];

        (new VendorsAssignRepository())->deleteAllItems($options);
    }
}
