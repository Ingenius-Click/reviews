<?php

namespace Ingenius\Reviews\Services;

use Ingenius\Core\Interfaces\IReviewVerifier;

/**
 * Null Review Verifier - allows all reviews without verification
 *
 * This is the default verifier that doesn't enforce any restrictions.
 * Useful when you want to allow reviews from all users regardless of
 * purchase history or other criteria.
 */
class NullReviewVerifier implements IReviewVerifier
{
    /**
     * Always allows reviews (no verification needed)
     *
     * @param mixed $reviewer The user/entity attempting to review
     * @param mixed $reviewable The entity being reviewed
     * @return bool Always returns true
     */
    public function canReview($reviewer, $reviewable): bool
    {
        return true;
    }

    /**
     * Returns empty metadata since no verification is performed
     *
     * @param mixed $reviewer The user/entity attempting to review
     * @param mixed $reviewable The entity being reviewed
     * @return array Empty array
     */
    public function getVerificationMetadata($reviewer, $reviewable): array
    {
        return [];
    }

    public function getFailedMessage(): string {
        return '';
    }
}
