<?php

namespace Ingenius\Reviews\Actions;

use Ingenius\Reviews\Models\Review;

/**
 * Action to list reviewable products with their review statistics
 *
 * This action is decoupled from specific product implementations and works
 * with any model that can be reviewed. It returns products with their
 * basic info (id, name, sku) and review statistics.
 */
class ListReviewableProductsAction
{
    /**
     * Handle the action to list reviewable products
     *
     * @param array $filters Pagination and filter parameters
     * @return mixed Paginated results from table_handler_paginate
     */
    public function handle(array $filters = [])
    {
        $productModel = config('reviews.product_model');

        // Start with base query for all products
        $query = $productModel::query();

        // Add review statistics using subqueries for better performance
        $query->select([
            'products.id',
            'products.name',
            'products.sku',
        ])->selectSub(
            Review::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true),
            'total_reviews'
        )->selectSub(
            Review::query()
                ->selectRaw('COALESCE(AVG(rating), 0)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true),
            'average_rating'
        )->selectSub(
            Review::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true)
                ->where('rating', 1),
            'rating_1_count'
        )->selectSub(
            Review::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true)
                ->where('rating', 2),
            'rating_2_count'
        )->selectSub(
            Review::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true)
                ->where('rating', 3),
            'rating_3_count'
        )->selectSub(
            Review::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true)
                ->where('rating', 4),
            'rating_4_count'
        )->selectSub(
            Review::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('reviewable_id', 'products.id')
                ->where('reviewable_type', $productModel)
                ->where('is_approved', true)
                ->where('rating', 5),
            'rating_5_count'
        );

        // Use the standard table handler paginate helper
        return table_handler_paginate($filters, $query);
    }
}
