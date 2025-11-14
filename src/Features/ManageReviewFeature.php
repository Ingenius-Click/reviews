<?php

namespace Ingenius\Reviews\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ManageReviewFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'manage-reviews';
    }

    public function getName(): string
    {
        return __('Manage reviews');
    }

    public function getDescription(): string
    {
        return __('Approve, reject, update, and delete reviews submitted by customers');
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
