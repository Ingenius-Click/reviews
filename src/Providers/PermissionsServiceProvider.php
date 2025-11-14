<?php

namespace Ingenius\Reviews\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Support\PermissionsManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Reviews\Constants\ReviewPermissions;

class PermissionsServiceProvider extends ServiceProvider {

    use RegistersConfigurations;

    /**
     * The package name.
     *
     * @var string
     */
    protected string $packageName = 'Reviews';

    /**
     * Boot the application events.
     */
    public function boot(PermissionsManager $permissionsManager): void
    {
        $this->registerPermissions($permissionsManager);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Register package-specific permission config
        $configPath = __DIR__ . '/../../config/permissions.php';

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'reviews.permissions');
            $this->registerConfig($configPath, 'reviews.permissions', 'reviews');
        }
    }

    /**
     * Register the package's permissions.
     */
    protected function registerPermissions(PermissionsManager $permissionsManager): void
    {
        // Register Reviews package permissions
        $permissionsManager->register(
            ReviewPermissions::REVIEWS_VIEW,
            'View reviews',
            $this->packageName,
            'tenant',
            'View reviews',
            'Reviews'
        );

        $permissionsManager->register(
            ReviewPermissions::REVIEWS_APPROVE,
            'Approve reviews',
            $this->packageName,
            'tenant',
            'Approve reviews',
            'Reviews'
        );

        $permissionsManager->register(
            ReviewPermissions::REVIEWS_REJECT,
            'Reject reviews',
            $this->packageName,
            'tenant',
            'Reject reviews',
            'Reviews'
        );

        $permissionsManager->register(
            ReviewPermissions::REVIEWS_DELETE,
            'Delete reviews',
            $this->packageName,
            'tenant',
            'Delete reviews',
            'Reviews'
        );
    }

}
