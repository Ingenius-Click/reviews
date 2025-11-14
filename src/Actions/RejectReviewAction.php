<?php

namespace Ingenius\Reviews\Actions;

use Exception;
use Illuminate\Support\Facades\Log;
use Ingenius\Reviews\Models\Review;

class RejectReviewAction {

    public function handle(Review $review, ?string $reason = null): Review {

        try {
            $review->reject($reason);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }

        return $review->refresh();
    }

}
