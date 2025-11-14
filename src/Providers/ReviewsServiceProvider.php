<?php

namespace Ingenius\Reviews\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Traits\RegistersMigrations;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Products\Services\ProductExtensionManager;
use Ingenius\Reviews\Extensions\ReviewsProductExtension;

class ReviewsServiceProvider extends ServiceProvider
{
    use RegistersMigrations, RegistersConfigurations;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/reviews.php', 'reviews');

        // Register configuration with the registry
        $this->registerConfig(__DIR__.'/../../config/reviews.php', 'reviews', 'reviews');

        // Register the route service provider
        $this->app->register(RouteServiceProvider::class);

        // Register the permissions service provider
        $this->app->register(PermissionsServiceProvider::class);

        // Register the product review verifier implementation
        $this->app->singleton('reviews.product_verifier', function ($app) {
            $verifierClass = config('reviews.product_review_verifier');

            // Check if the class exists before instantiating
            if (!class_exists($verifierClass)) {
                // Fallback to NullReviewVerifier if configured class doesn't exist
                $verifierClass = \Ingenius\Reviews\Services\NullReviewVerifier::class;
            }

            return new $verifierClass();
        });

        // Register the product extension
        $this->app->afterResolving(ProductExtensionManager::class, function (ProductExtensionManager $manager) {
            $manager->register(new ReviewsProductExtension());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register migrations with the registry
        $this->registerMigrations(__DIR__.'/../../database/migrations', 'reviews');
        
        // Check if there's a tenant migrations directory and register it
        $tenantMigrationsPath = __DIR__.'/../../database/migrations/tenant';
        if (is_dir($tenantMigrationsPath)) {
            $this->registerTenantMigrations($tenantMigrationsPath, 'reviews');
        }
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'reviews');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/reviews.php' => config_path('reviews.php'),
        ], 'reviews-config');
        
        // Publish views
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/reviews'),
        ], 'reviews-views');
        
        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'reviews-migrations');
    }
}