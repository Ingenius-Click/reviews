<?php

use Illuminate\Support\Facades\Route;
use Ingenius\Reviews\Http\Controllers\ReviewController;
use Ingenius\Reviews\Http\Controllers\ReviewCrudController;
use Ingenius\Reviews\Http\Controllers\ReviewableController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register tenant-specific routes for your package.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the tenant middleware for multi-tenancy support.
|
*/

// Route::get('tenant-example', function () {
//     return 'Hello from tenant-specific route! Current tenant: ' . tenant('id');
// });

Route::middleware('api')
    ->prefix('api')->group(function() {
        Route::prefix('reviews')->group(function(){

            Route::middleware(['tenant.user'])->group(function(){
                Route::get('/', [ReviewCrudController::class, 'index']);
                Route::post('review-product', [ReviewController::class, 'reviewProduct']);
                Route::post('/{review}/approve', [ReviewCrudController::class, 'approve']);
                Route::post('/{review}/reject', [ReviewCrudController::class, 'reject']);
            });

        });

        // Reviewable endpoints - decoupled from specific reviewable types
        Route::prefix('reviewables')->group(function(){
            Route::middleware(['tenant.user'])->group(function(){
                Route::get('products', [ReviewableController::class, 'products']);
            });
        });
    });