<?php

namespace App\Services\Notifications;

use App\Classes\Enum\PermissionsNames;
use App\Models\User;
use App\Notifications\NewCommentForReviewNotification;
use App\Notifications\NewReviewForProductNotification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Modules\Reviews\Entities\Comments;
use Modules\Reviews\Entities\Reviews;
use App\Classes\Enum\Vendors\UserType;

class ReviewService
{
    /**
     * @param Reviews $review
     * @return bool
     */
    public function sendCreateReviewNotify(Reviews $review)
    {
        if (getSetting('send_review_notification') == 1) {
            $users = null;
            if (getSetting('enable_vendors')) {
                $sendToUsersIds = [];
                $modelClass = $review->model_type;
                if (class_exists($review->model_type)) {
                    $model = $modelClass::find($review->model_id);
                    if (class_basename($model) == 'Products') {
                        $vendors = $model->vendors;
                        foreach($vendors as $vendor) {
                            $ids = $vendor->users()->where('user_type', UserType::MANAGER)->pluck('users.id')->toArray();
                            $sendToUsersIds = array_merge($ids, $sendToUsersIds);
                        }
                    }
                }

                $sendToUsersIds = array_unique($sendToUsersIds);
                if ($sendToUsersIds) {
                    $users = User::find($sendToUsersIds);
                }
            } else {
                $users = User::permission(PermissionsNames::MANAGE_REVIEWS)->get();
            }

            if ($users) {
                FacadesNotification::send($users, new NewReviewForProductNotification($review));
            }
        }
        return false;
    }

    /**
     * @param Comments $comment
     * @return bool
     */
    public function sendCreateCommentNotify(Comments $comment)
    {
        if (getSetting('send_comment_review_notification')) {
            $modelClass = $comment->model_type;
            if (class_exists($comment->model_type)) {
                $model = $modelClass::find($comment->model_id);
                if (class_basename($model) == 'Reviews') {
                    if ($sender = $model->getSender()) {
                        return $sender->notify(new NewCommentForReviewNotification($model, $comment));
                    }
                }
            }
        }

        return false;
    }
}
