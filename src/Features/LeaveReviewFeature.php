<?php

namespace Ingenius\Reviews\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class LeaveReviewFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'leave-reviews';
    }

    public function getName(): string
    {
        return __('Leave reviews');
    }

    public function getDescription(): string
    {
        return __('Submit, edit, and manage your own product reviews');
    }

    public function getGroup(): string
    {
        return __('Reviews');
    }

    public function getPackage(): string
    {
        return 'reviews';
    }

    public function isBasic(): bool
    {
        return false;
    }
}
