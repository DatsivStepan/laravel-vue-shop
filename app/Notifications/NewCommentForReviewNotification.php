<?php

namespace App\Notifications;

use App\Models\Products;
use App\Notifications\Templates\NewCommentForReviewTemplate;
use App\Notifications\Templates\NewReviewForProductTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Notifications\Notification;
use Modules\Notifications\Services\Templates\Template;
use Modules\Reviews\Entities\Comments;
use Modules\Reviews\Entities\Reviews;

class NewCommentForReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Comments */
    private $comments;

    /** @var Reviews */
    private $reviews;

    public function __construct(Reviews $reviews, Comments $comments)
    {
        $this->comments = $comments;
        $this->reviews = $reviews;

        parent::__construct();
    }

    public function getTagsDict(): array
    {
        $product = Products::where('id', $this->reviews->model_id)->first();
        $sender = $this->comments->getSender();
        return [
            'product' => $product->name,
            'sender_full_name' => $sender ? $sender->getFullName() : '',
            'recipient_full_name' => '',
            'review_text' => $this->comments->text
        ];
    }

    protected function getTemplate(): Template
    {
        return new NewCommentForReviewTemplate($this->getTagsDict());
    }
}
