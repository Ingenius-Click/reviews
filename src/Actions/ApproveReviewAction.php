<?php

namespace Ingenius\Reviews\Actions;

use Exception;
use Illuminate\Support\Facades\Log;
use Ingenius\Reviews\Models\Review;

class ApproveReviewAction {

    public function handle(Review $review): Review {

        try {
            $review->approve();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }

        return $review->refresh();
    }

}