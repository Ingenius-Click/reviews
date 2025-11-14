<?php

namespace Ingenius\Reviews\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Ingenius\Core\Helpers\AuthHelper;
use Ingenius\Core\Http\Controllers\Controller;
use Ingenius\Reviews\Actions\AddReviewAction;

class ReviewController extends Controller {

    public function reviewProduct(Request $request, AddReviewAction $action): JsonResponse {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $user = AuthHelper::getUser();

        $productModelClass = Config::get('reviews.product_model');

        if(!class_exists($productModelClass)) {
            abort(404, __('Product Model Class not found'));
        }

        $reviewable = $productModelClass::findOrFail($validated['product_id']);

        // Resolve the verifier from the service container
        $verifier = App::make('reviews.product_verifier');

        if(!$verifier->canReview($user, $reviewable)) {
            abort(400, $verifier->getFailedMessage());
        }

        $review = $action->handle($reviewable, $validated['rating'], $validated['comment']);

        return Response::api(data: $review, message: __('Product reviewed successfully'));
    }

}