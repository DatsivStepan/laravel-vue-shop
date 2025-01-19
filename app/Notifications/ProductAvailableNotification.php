<?php

namespace App\Notifications;

use App\Models\ProductWaitlist;
use App\Notifications\Templates\ProductAvailableTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Notifications\Notification;
use Modules\Notifications\Services\Templates\Template;

class ProductAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var ProductWaitlist */
    private $productWaitList;

    public function __construct(ProductWaitlist $productWaitlist)
    {
        $this->productWaitList = $productWaitlist;

        parent::__construct();
    }

    public function getTagsDict(): array
    {
        return [
            'user_full_name' => $this->productWaitList->user->name,
            'product' => $this->productWaitList->product()->select('name')->first()->name,
        ];
    }

    protected function getTemplate(): Template
    {
        return new ProductAvailableTemplate($this->getTagsDict());
    }

}
