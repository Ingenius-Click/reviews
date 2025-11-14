<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // Creates reviewable_id and reviewable_type
            $table->morphs('reviewer'); // Creates reviewer_id and reviewer_type (for User or other entities)
            $table->unsignedTinyInteger('rating'); // 1-5 stars
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false); // If purchase is verified
            $table->boolean('is_approved')->default(false); // For moderation
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_rejected')->default(false); // For rejected reviews
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
