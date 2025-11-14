<?php

namespace Ingenius\Reviews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reviewable_id',
        'reviewable_type',
        'reviewer_id',
        'reviewer_type',
        'rating',
        'title',
        'comment',
        'is_verified',
        'is_approved',
        'approved_at',
        'is_rejected',
        'rejected_at',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'is_rejected' => 'boolean',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent reviewable model (Product, Service, etc).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the reviewer model (User, Guest, etc).
     */
    public function reviewer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)->where('is_rejected', false);
    }

    public function scopeRejected($query) {
        return $query->where('is_approved', false)->where('is_rejected', true);
    }

    public function scopeNew($query) {
        return $query->where('is_approved', false)->where('is_rejected', false);
    }

    /**
     * Scope a query to only include verified reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to filter by rating.
     */
    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope a query to filter by minimum rating.
     */
    public function scopeMinimumRating($query, int $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Approve the review.
     */
    public function approve(): bool
    {
        $this->is_approved = true;
        $this->approved_at = now();
        $this->is_rejected = false;
        $this->rejected_at = null;
        $this->rejection_reason = null;
        return $this->save();
    }

    /**
     * Unapprove the review.
     */
    public function unapprove(): bool
    {
        $this->is_approved = false;
        $this->approved_at = null;
        return $this->save();
    }

    /**
     * Reject the review.
     */
    public function reject(string $reason = null): bool
    {
        $this->is_rejected = true;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        $this->is_approved = false;
        $this->approved_at = null;
        return $this->save();
    }

    /**
     * Unreject the review.
     */
    public function unreject(): bool
    {
        $this->is_rejected = false;
        $this->rejected_at = null;
        $this->rejection_reason = null;
        return $this->save();
    }

    /**
     * Mark the review as verified.
     */
    public function verify(): bool
    {
        $this->is_verified = true;
        return $this->save();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Validate rating is between 1 and 5
        static::saving(function ($review) {
            if ($review->rating < 1 || $review->rating > 5) {
                throw new \InvalidArgumentException('Rating must be between 1 and 5 stars.');
            }
        });
    }
}