<?php

namespace Ingenius\Reviews\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ingenius\Core\Interfaces\HasCustomerProfile;

class ReviewWithReviewerResource extends JsonResource {

    public function toArray(\Illuminate\Http\Request $request): array {

        $data = [
            'id' => $this->id,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
        ];

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

        return $data;
    }

}