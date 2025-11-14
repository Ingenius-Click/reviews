<?php

namespace Ingenius\Reviews\Policies;

use Ingenius\Reviews\Constants\ReviewPermissions;
use Ingenius\Reviews\Models\Review;

class ReviewPolicy
{
    public function viewAny($user): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ReviewPermissions::REVIEWS_VIEW);
        }

        return false;
    }

    public function view($user, Review $review): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ReviewPermissions::REVIEWS_VIEW);
        }

        return false;
    }

    public function approve($user, Review $review): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ReviewPermissions::REVIEWS_APPROVE);
        }

        return false;
    }

    public function reject($user, Review $review): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ReviewPermissions::REVIEWS_REJECT);
        }

        return false;
    }

    public function delete($user, Review $review): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ReviewPermissions::REVIEWS_DELETE);
        }

        return false;
    }
}
