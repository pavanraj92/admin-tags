<?php

namespace admin\tags;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TagServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/Tags/resources/views'), // Published module views first
            resource_path('views/admin/tag'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'tag');

        $this->mergeConfigFrom(__DIR__.'/../config/tag.php', 'tag.constants');
        
        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/Tags/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Tags/resources/views'), 'tags-module');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // Also load migrations from published module if they exist
        if (is_dir(base_path('Modules/Tags/database/migrations'))) {
            $this->loadMigrationsFrom(base_path('Modules/Tags/database/migrations'));
        }

        // Also merge config from published module if it exists
        if (file_exists(base_path('Modules/Tags/config/tag.php'))) {
            $this->mergeConfigFrom(base_path('Modules/Tags/config/tag.php'), 'tag.constants');
        }
        // Only publish automatically during package installation, not on every request
        // Use 'php artisan tags:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();
        
        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../database/migrations' => base_path('Modules/Tags/database/migrations'),
            __DIR__ . '/../resources/views' => base_path('Modules/Tags/resources/views/'),
        ], 'tag');
       
        $this->registerAdminRoutes();

    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $admin = DB::table('admins')
            ->orderBy('created_at', 'asc')
            ->first();
            
        $slug = $admin->website_slug ?? 'admin';

        $routeFile = base_path('Modules/Tags/routes/web.php');
        if (!file_exists($routeFile)) {
            $routeFile = __DIR__ . '/routes/web.php'; // fallback to package route
        }

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group($routeFile);
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\tags\Console\Commands\PublishTagsModuleCommand::class,
                \admin\tags\Console\Commands\CheckModuleStatusCommand::class,
                \admin\tags\Console\Commands\DebugTagsCommand::class,
                \admin\tags\Console\Commands\TestViewResolutionCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/TagManagerController.php' => base_path('Modules/Tags/app/Http/Controllers/Admin/TagManagerController.php'),
            
            // Models
            __DIR__ . '/../src/Models/Tag.php' => base_path('Modules/Tags/app/Models/Tag.php'),
            
            // Requests
            __DIR__ . '/../src/Requests/TagCreateRequest.php' => base_path('Modules/Tags/app/Http/Requests/TagCreateRequest.php'),
            __DIR__ . '/../src/Requests/TagUpdateRequest.php' => base_path('Modules/Tags/app/Http/Requests/TagUpdateRequest.php'),
            
            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/Tags/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));
                
                // Read the source file
                $content = File::get($source);
                
                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);
                
                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\tags\\Controllers;' => 'namespace Modules\\Tags\\app\\Http\\Controllers\\Admin;',
            'namespace admin\\tags\\Models;' => 'namespace Modules\\Tags\\app\\Models;',
            'namespace admin\\tags\\Requests;' => 'namespace Modules\\Tags\\app\\Http\\Requests;',
            
            // Use statements transformations
            'use admin\\tags\\Controllers\\' => 'use Modules\\Tags\\app\\Http\\Controllers\\Admin\\',
            'use admin\\tags\\Models\\' => 'use Modules\\Tags\\app\\Models\\',
            'use admin\\tags\\Requests\\' => 'use Modules\\Tags\\app\\Http\\Requests\\',
            
            // Class references in routes
            'admin\\tags\\Controllers\\TagManagerController' => 'Modules\\Tags\\app\\Http\\Controllers\\Admin\\TagManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'Models')) {
            $content = $this->transformModelNamespaces($content);
        } elseif (str_contains($sourceFile, 'Requests')) {
            $content = $this->transformRequestNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        $content = str_replace(
            'use admin\\tags\\Models\\Tag;',
            'use Modules\\Tags\\app\\Models\\Tag;',
            $content
        );
        
        $content = str_replace(
            'use admin\\tags\\Requests\\TagCreateRequest;',
            'use Modules\\Tags\\app\\Http\\Requests\\TagCreateRequest;',
            $content
        );
        
        $content = str_replace(
            'use admin\\tags\\Requests\\TagUpdateRequest;',
            'use Modules\\Tags\\app\\Http\\Requests\\TagUpdateRequest;',
            $content
        );

        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\tags\\Controllers\\TagManagerController',
            'Modules\\Tags\\app\\Http\\Controllers\\Admin\\TagManagerController',
            $content
        );

        return $content;
    }
}
