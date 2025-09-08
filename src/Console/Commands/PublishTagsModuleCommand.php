<?php

namespace admin\tags\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishTagsModuleCommand extends Command
{
    protected $signature = 'tags:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish Tags module files with proper namespace transformation';

    public function handle()
    {
        $this->info('Publishing Tags module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/Tags');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();

        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'tag',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('Tags module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/tags/src

        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/TagManagerController.php' => base_path('Modules/Tags/app/Http/Controllers/Admin/TagManagerController.php'),

            // Models
            $basePath . '/Models/Tag.php' => base_path('Modules/Tags/app/Models/Tag.php'),

            // Requests
            $basePath . '/Requests/TagCreateRequest.php' => base_path('Modules/Tags/app/Http/Requests/TagCreateRequest.php'),
            $basePath . '/Requests/TagUpdateRequest.php' => base_path('Modules/Tags/app/Http/Requests/TagUpdateRequest.php'),

            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/Tags/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));

                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);

                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

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
            $content = str_replace('use admin\\tags\\Models\\Tag;', 'use Modules\\Tags\\app\\Models\\Tag;', $content);
            $content = str_replace('use admin\\tags\\Requests\\TagCreateRequest;', 'use Modules\\Tags\\app\\Http\\Requests\\TagCreateRequest;', $content);
            $content = str_replace('use admin\\tags\\Requests\\TagUpdateRequest;', 'use Modules\\Tags\\app\\Http\\Requests\\TagUpdateRequest;', $content);
            $content = str_replace(
                'use admin\admin_auth\Traits\HasSeo;',
                'use Modules\\AdminAuth\\app\\Traits\\HasSeo;',
                $content
            );
        } elseif (str_contains($sourceFile, 'Models')) {
            // Transform admin_auth namespaces in models
            $content = str_replace(
                'use admin\admin_auth\Models\Seo;',
                'use Modules\\AdminAuth\\app\\Models\\Seo;',
                $content
            );
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\Tags\\'])) {
            $composer['autoload']['psr-4']['Modules\\Tags\\'] = 'Modules/Tags/app/';

            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}