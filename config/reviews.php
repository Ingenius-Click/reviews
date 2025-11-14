<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify configuration options for the reviews package.
    |
    */

    'product_model' => env('PRODUCT_MODEL', 'Ingenius\Products\Models\Product'),

    /*
    |--------------------------------------------------------------------------
    | Review Model
    |--------------------------------------------------------------------------
    |
    | Specify the Review model class used by the system.
    | This allows other packages to access reviews without creating direct dependencies.
    |
    */
    'review_model' => env('REVIEW_MODEL', 'Ingenius\Reviews\Models\Review'),

    /*
    |--------------------------------------------------------------------------
    | Order Model
    |--------------------------------------------------------------------------
    |
    | Specify the Order model class used by the system for order-based verification.
    | This is used by OrderBasedProductReviewVerifier to check purchase history.
    |
    */
    'order_model' => env('ORDER_MODEL', 'Ingenius\Orders\Models\Order'),

    /*
    |--------------------------------------------------------------------------
    | Product Review Verifier Implementation
    |--------------------------------------------------------------------------
    |
    | Specify which class handles product review verification logic.
    | The default OrderBasedProductReviewVerifier requires users to have
    | purchased the product before leaving a review.
    | Use NullReviewVerifier to allow reviews without verification.
    |
    | Examples:
    | - \Ingenius\Reviews\Services\OrderBasedProductReviewVerifier::class (default)
    | - \Ingenius\Reviews\Services\NullReviewVerifier::class
    |
    */
    'product_review_verifier' => env(
        'PRODUCT_REVIEW_VERIFIER',
        \Ingenius\Reviews\Services\OrderBasedProductReviewVerifier::class
    ),
];