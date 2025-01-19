<?php

namespace App\Notifications;

use App\Models\Products;
use App\Notifications\Templates\NewReviewForProductTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Notifications\Notification;
use Modules\Notifications\Services\Templates\Template;
use Modules\Reviews\Entities\Reviews;

class NewReviewForProductNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Reviews */
    private $review;

    public function __construct(Reviews $review)
    {
        $this->review = $review;

        parent::__construct();
    }

    public function getTagsDict(): array
    {
        $product = Products::where('id', $this->review->model_id)->first();
        $sender = $this->review->getSender();
        return [
            'product' => $product->name,
            'sender_full_name' => $sender ? $sender->getFullName() : '',
            'recipient_full_name' => '',
            'review_text' => $this->review->text
        ];
    }

    protected function getTemplate(): Template
    {
        return new NewReviewForProductTemplate($this->getTagsDict());
    }
}
