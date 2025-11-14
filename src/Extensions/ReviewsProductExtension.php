<?php

namespace Ingenius\Reviews\Extensions;

use Illuminate\Database\Eloquent\Builder;
use Ingenius\Products\Extensions\BaseProductExtension;
use Ingenius\Reviews\Models\Review;

/**
 * Reviews Product Extension
 *
 * Extends product data with review information including:
 * - Average rating
 * - Total review count
 * - Rating distribution (count per star rating)
 */
class ReviewsProductExtension extends BaseProductExtension
{
    /**
     * Extend the product array with review data
     */
    public function extendProductArray($product, array $productArray): array
    {
        $reviewModelClass = config('reviews.review_model', Review::class);

        if (!class_exists($reviewModelClass)) {
            return $productArray;
        }

        // Get review statistics for this product
        $reviews = $reviewModelClass::query()
            ->where('reviewable_id', $product->id)
            ->where('reviewable_type', get_class($product))
            ->where('is_approved', true)
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 2) : 0;

        // Calculate rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = $reviews->where('rating', $i)->count();
        }

        // Add review data to product array
        $productArray['reviews'] = [
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'rating_distribution' => $ratingDistribution,
        ];

        return $productArray;
    }

    /**
     * Don't need to modify the query for now
     * We could add eager loading here in the future if needed
     */
    public function extendProductQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Run this extension with default priority
     */
    public function getPriority(): int
    {
        return 50;
    }

    /**
     * Extension name
     */
    public function getName(): string
    {
        return 'ReviewsExtension';
    }
}
