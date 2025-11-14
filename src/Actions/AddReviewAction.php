<?php

namespace Ingenius\Reviews\Actions;

use Illuminate\Support\Facades\Config;
use Ingenius\Core\Helpers\AuthHelper;
use Ingenius\Reviews\Models\Review;

class AddReviewAction {


    public function handle($reviewable, int $rating, ?string $comment): Review {

        $user = AuthHelper::getUser();

        $review = Review::create([
            'reviewable_id' => $reviewable->id,
            'reviewable_type' => get_class($reviewable),
            'reviewer_id' => $user->id,
            'reviewer_type' => get_class($user),
            'rating' => $rating,
            'comment' => $comment
        ]);

        return $review;
    }
}