<?php

namespace Ingenius\Reviews\Services;

use Ingenius\Reviews\Models\Review;

/**
 * Product Review Statistics Service
 *
 * Calculates and provides review statistics for products including:
 * - Average rating
 * - Total review count
 * - Rating distribution (count per star rating)
 */
class ProductReviewStatsService
{
    /**
     * Get review statistics for a product
     *
     * @param mixed $product The product instance
     * @return array Review statistics array
     */
    public function getProductReviewStats($product): array
    {
        $reviewModelClass = config('reviews.review_model', Review::class);

        if (!class_exists($reviewModelClass)) {
            return $this->getEmptyStats();
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

        return [
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'rating_distribution' => $ratingDistribution,
        ];
    }

    /**
     * Extend product array with review statistics
     *
     * This method is designed to be used as a hook handler
     *
     * @param array $productArray The product array to extend
     * @param array $context Context data containing the product instance
     * @return array Extended product array with review data
     */
    public function extendProductArray(array $data, array $context): array
    {
        if (!isset($context['product_id']) || !isset($context['product_class'])) {
            return [];
        }

        $product = $context['product_class']::find($context['product_id']);
        $data['reviews'] = $this->getProductReviewStats($product);

        return $data;
    }

    /**
     * Get empty statistics structure
     *
     * @return array
     */
    protected function getEmptyStats(): array
    {
        return [
            'average_rating' => 0,
            'total_reviews' => 0,
            'rating_distribution' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
            ],
        ];
    }
}
