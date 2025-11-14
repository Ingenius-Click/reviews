<?php

namespace Ingenius\Reviews\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Ingenius\Core\Helpers\AuthHelper;
use Ingenius\Reviews\Actions\ListReviewableProductsAction;
use Ingenius\Reviews\Http\Resources\ReviewableProductResource;
use Ingenius\Reviews\Models\Review;

class ReviewableController extends Controller
{
    use AuthorizesRequests;
    /**
     * List all reviewable products with their review statistics
     *
     * Returns products with:
     * - id, name, sku
     * - Review statistics (total reviews, average rating, rating distribution)
     *
     * @param Request $request
     * @param ListReviewableProductsAction $listReviewableProductsAction
     * @return JsonResponse
     */
    public function products(Request $request, ListReviewableProductsAction $listReviewableProductsAction): JsonResponse
    {
        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Review::class);

        $paginator = $listReviewableProductsAction->handle($request->all());

        $reviewableProducts = $paginator->through(fn($product) => new ReviewableProductResource($product));

        return Response::api(
            data: $reviewableProducts,
            message: 'Reviewable products fetched successfully'
        );
    }

    // Future methods can be added here for other reviewable types:
    // public function services(Request $request, ListReviewableServicesAction $action): JsonResponse { ... }
    // public function courses(Request $request, ListReviewableCoursesAction $action): JsonResponse { ... }
}
