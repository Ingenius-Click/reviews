<?php

namespace Ingenius\Reviews\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ingenius\Core\Interfaces\IBaseProductibleData;
use Ingenius\Core\Interfaces\IPurchasable;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\HasCustomerProfile;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'is_verified' => $this->is_verified,
            'is_approved' => $this->is_approved,
            'approved_at' => $this->approved_at,
            'is_rejected' => $this->is_rejected,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add reviewer information using interfaces
        $reviewer = $this->reviewer;

        if ($reviewer) {
            $reviewerData = [
                'id' => $this->reviewer_id,
                'type' => $this->reviewer_type,
            ];

            // Use HasCustomerProfile interface for customer information
            if ($reviewer instanceof HasCustomerProfile) {
                $reviewerData['name'] = $reviewer->getFullName();
                $reviewerData['email'] = $reviewer->getEmail();
                $reviewerData['phone'] = $reviewer->getPhone();
            }

            $data['reviewer'] = $reviewerData;
        } else {
            $data['reviewer'] = null;
        }

        // Add reviewable (product) information using interfaces
        $reviewable = $this->reviewable;

        if ($reviewable) {
            $productData = [
                'id' => $this->reviewable_id,
                'type' => $this->reviewable_type,
            ];

            // Use IBaseProductibleData interface for basic product information
            if ($reviewable instanceof IBaseProductibleData) {
                $productData['sku'] = $reviewable->getSku();
                $productData['images'] = $reviewable->images();
            }

            // Use IPurchasable interface for pricing information
            if ($reviewable instanceof IPurchasable) {
                $productData['name'] = $reviewable->getName();
            }

            $data['product'] = $productData;
        } else {
            $data['product'] = null;
        }

        return $data;
    }
}
