<?php

namespace Ingenius\Reviews\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Ingenius\Core\Http\Controllers\Controller;
use Ingenius\Reviews\Actions\ApproveReviewAction;
use Ingenius\Reviews\Actions\PaginateReviewsAction;
use Ingenius\Reviews\Actions\RejectReviewAction;
use Ingenius\Reviews\Http\Requests\RejectReviewRequest;
use Ingenius\Reviews\Http\Resources\ReviewResource;
use Ingenius\Reviews\Models\Review;

class ReviewCrudController extends Controller {
    use AuthorizesRequests;

    public function index(Request $request, PaginateReviewsAction $paginateReviews): JsonResponse {
        $user = \Ingenius\Core\Helpers\AuthHelper::getUser();
        $this->authorizeForUser($user, 'viewAny', Review::class);

        $filters = $request->all();
        $reviews = $paginateReviews->handle($filters);

        return Response::api(
            data: $reviews->through(fn($review) => new ReviewResource($review)),
            message: __('Reviews fetched successfully.')
        );
    }

    public function approve(Request $request, Review $review, ApproveReviewAction $action): JsonResponse {

        $user = \Ingenius\Core\Helpers\AuthHelper::getUser();
        $this->authorizeForUser($user, 'approve', $review);

        if($review->is_approved) {
            return Response::api(message: __('Review already approved.'), code: 400);
        }

        $r = $action->handle($review);

        return Response::api(data: $r, message: __('Review approved successfully.'));
    }

    public function reject(RejectReviewRequest $request, Review $review, RejectReviewAction $action): JsonResponse {

        $user = \Ingenius\Core\Helpers\AuthHelper::getUser();
        $this->authorizeForUser($user, 'reject', $review);

        if($review->is_rejected) {
            return Response::api(message: __('Review already rejected.'), code: 400);
        }

        $r = $action->handle($review, $request->input('reason'));

        return Response::api(data: $r, message: __('Review rejected successfully.'));
    }

    public function destroy(Request $request, Review $review): JsonResponse {

        $user = \Ingenius\Core\Helpers\AuthHelper::getUser();
        $this->authorizeForUser($user, 'delete', $review);

        $review->delete();

        return Response::api(message: __('Review deleted successfully.'));
    }

}