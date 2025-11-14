<?php

namespace Ingenius\Reviews\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Ingenius\Reviews\Http\Resources\ReviewWithReviewerResource;
use Ingenius\Reviews\Models\Review;

class GetReviewableReviewsAction {

    public function handle($reviewable, array $filters = []): LengthAwarePaginator {

        $query = Review::query()->approved()
                    ->where('reviewable_id', $reviewable->id)
                    ->where('reviewable_type', get_class($reviewable))
                    ->latest()
                    ;

        return table_handler_paginate($filters, $query)->through(fn($review) => new ReviewWithReviewerResource($review));
    }

}