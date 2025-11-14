<?php

namespace Ingenius\Reviews\Services;

use Ingenius\Core\Interfaces\IReviewVerifier;

/**
 * Order-Based Product Review Verifier
 *
 * Verifies that a user has purchased a product before allowing them to review it.
 * Users can review a product multiple times if they've purchased it multiple times.
 *
 * Logic:
 * - User can review if: total_reviews < total_completed_purchases
 * - This means if they bought it 3 times, they can leave up to 3 reviews
 * - First purchase = can leave 1st review
 * - Each subsequent purchase = can leave another review
 */
class OrderBasedProductReviewVerifier implements IReviewVerifier
{
    /**
     * Check if a reviewer can review a specific product
     *
     * Verifies that the reviewer's total reviews for a product is less than
     * the total completed orders where they purchased that product.
     *
     * @param mixed $reviewer The user/entity attempting to review
     * @param mixed $reviewable The product being reviewed
     * @return bool True if the reviewer can leave another review
     */
    public function canReview($reviewer, $reviewable): bool
    {
        // Get the Order model class from config
        $orderModelClass = config('reviews.order_model');

        if (!$orderModelClass || !class_exists($orderModelClass)) {
            // If no order model configured, fallback to allowing reviews
            return true;
        }

        // Get the completed order status class from config
        $completedStatusClass = config('orders.completed_order_status_class');

        // If no completed status class is configured, default to 'completed'
        $completedStatusIdentifier = 'completed';
        if ($completedStatusClass && class_exists($completedStatusClass)) {
            $statusInstance = new $completedStatusClass();
            if (method_exists($statusInstance, 'getIdentifier')) {
                $completedStatusIdentifier = $statusInstance->getIdentifier();
            }
        }

        // Count how many times the user has purchased this product (completed orders)
        $totalCompletedPurchases = $orderModelClass::query()
            ->whereHas('products', function ($query) use ($reviewable) {
                $query->where('productible_id', $reviewable->id)
                    ->where('productible_type', get_class($reviewable));
            })
            ->where('userable_id', $reviewer->id)
            ->where('userable_type', get_class($reviewer))
            ->where('status', $completedStatusIdentifier)
            ->count();

        // If they haven't purchased it, they can't review
        if ($totalCompletedPurchases === 0) {
            return false;
        }

        // Get the Review model class from config
        $reviewModelClass = config('reviews.review_model');

        // If Review model class doesn't exist, allow the review (fallback behavior)
        if (!$reviewModelClass || !class_exists($reviewModelClass)) {
            return true;
        }

        // Count how many times the user has reviewed this product
        $totalReviews = $reviewModelClass::query()
            ->where('reviewable_id', $reviewable->id)
            ->where('reviewable_type', get_class($reviewable))
            ->where('reviewer_id', $reviewer->id)
            ->where('reviewer_type', get_class($reviewer))
            ->count();

        // User can review if their total reviews is less than their total purchases
        return $totalReviews < $totalCompletedPurchases;
    }

    /**
     * Get verification metadata for the review
     *
     * Returns information about the most recent completed order
     * containing the reviewed product.
     *
     * @param mixed $reviewer The user/entity attempting to review
     * @param mixed $reviewable The product being reviewed
     * @return array Metadata including order_id, order_number, and purchase_date
     */
    public function getVerificationMetadata($reviewer, $reviewable): array
    {
        // Get the Order model class from config
        $orderModelClass = config('reviews.order_model');

        if (!$orderModelClass || !class_exists($orderModelClass)) {
            return [];
        }

        // Get the completed order status class from config
        $completedStatusClass = config('orders.completed_order_status_class');

        $completedStatusIdentifier = 'completed';
        if ($completedStatusClass && class_exists($completedStatusClass)) {
            $statusInstance = new $completedStatusClass();
            if (method_exists($statusInstance, 'getIdentifier')) {
                $completedStatusIdentifier = $statusInstance->getIdentifier();
            }
        }

        // Find the most recent completed order with this product
        $order = $orderModelClass::query()
            ->whereHas('products', function ($query) use ($reviewable) {
                $query->where('productible_id', $reviewable->id)
                    ->where('productible_type', get_class($reviewable));
            })
            ->where('userable_id', $reviewer->id)
            ->where('userable_type', get_class($reviewer))
            ->where('status', $completedStatusIdentifier)
            ->latest()
            ->first();

        if (!$order) {
            return [];
        }

        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'purchase_date' => $order->created_at->toDateString(),
            'verified_purchase' => true,
        ];
    }

    public function getFailedMessage(): string
    {
        return __('You need to purchase this product before you can review it.');
    }
}
