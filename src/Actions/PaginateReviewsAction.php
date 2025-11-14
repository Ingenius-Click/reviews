<?php

namespace Ingenius\Reviews\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Ingenius\Reviews\Models\Review;

class PaginateReviewsAction {

    public function handle(array $filters = []): LengthAwarePaginator {

        $status = $filters['status'] ?? null;
        $productId = $filters['product_id'] ?? null;

        $query = Review::query()->with(['reviewable', 'reviewer']);

        // Filter by status
        switch ($status) {
            case 'new':
                $query->new();
                break;
            case 'approved':
                $query->approved();
                break;
            case 'rejected':
                $query->rejected();
                break;

            default:
                break;
        }

        // Filter by product ID
        if ($productId) {
            $query->where('reviewable_id', $productId)
                  ->where('reviewable_type', config('reviews.product_model'));
        }

        return table_handler_paginate($filters, $query);
    }

}