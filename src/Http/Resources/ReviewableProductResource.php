<?php

namespace Ingenius\Reviews\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for transforming reviewable products
 *
 * This resource is fully decoupled and works with any product model.
 * It includes basic product information (id, name, sku) and review statistics.
 */
class ReviewableProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'review_stats' => [
                'total_reviews' => (int) ($this->total_reviews ?? 0),
                'average_rating' => round((float) ($this->average_rating ?? 0), 2),
                'rating_distribution' => [
                    1 => (int) ($this->rating_1_count ?? 0),
                    2 => (int) ($this->rating_2_count ?? 0),
                    3 => (int) ($this->rating_3_count ?? 0),
                    4 => (int) ($this->rating_4_count ?? 0),
                    5 => (int) ($this->rating_5_count ?? 0),
                ],
            ],
        ];
    }
}
