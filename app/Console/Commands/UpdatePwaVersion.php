<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UpdatePwaVersion extends Command
{
    protected $signature = 'pwa:version
                            {--new-version= : New version number (auto-generate if not provided)}
                            {--force : Force update even if version is same}
                            {--dry-run : Show what would be changed without actually updating}';

    protected $description = 'Update PWA version in manifest and service worker';

    public function handle()
    {
        $startTime = microtime(true);
        $this->info('🚀 Updating PWA version...');

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Get current version
        $currentVersion = $this->getCurrentVersion();
        $this->info("📌 Current version: {$currentVersion}");

        // Generate new version
        $newVersion = $this->option('new-version');
        if (!$newVersion) {
            $newVersion = $this->generateVersion();
        }

        $this->info("📌 New version: {$newVersion}");

        // Check if update is needed
        if ($currentVersion === $newVersion && !$force) {
            $this->info('✅ Version is already up to date. No changes needed.');
            $this->info('   Use --force to force update anyway.');
            return 0;
        }

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes will be made');
            $this->info('   Would update from ' . $currentVersion . ' to ' . $newVersion);

            // Show what would be changed
            $this->newLine();
            $this->info('📝 Changes that would be made:');
            $this->line('   • service-worker.js: APP_VERSION and CACHE_NAME');
            $this->line('   • manifest.json: start_url, id, and icon URLs');
            return 0;
        }

        // Update files
        $updated = false;

        // Update service-worker.js
        if ($this->updateServiceWorker($currentVersion, $newVersion)) {
            $updated = true;
            $this->info('✅ Updated service-worker.js');
        }

        // Update manifest.json
        if ($this->updateManifest($currentVersion, $newVersion)) {
            $updated = true;
            $this->info('✅ Updated manifest.json');
        }

        if ($updated) {
            $executionTime = round(microtime(true) - $startTime, 2);
            $this->info("⏱️  Update completed in {$executionTime} seconds");

            // Log::info('PWA version updated', [
            //     'old_version' => $currentVersion,
            //     'new_version' => $newVersion,
            //     'execution_time' => $executionTime
            // ]);
        } else {
            $this->warn('⚠️  No files were updated. Check if files exist.');
        }

        $this->newLine();
        $this->info('📌 Next steps:');
        $this->info('   1. Deploy to server');
        $this->info('   2. Users will get update automatically when they open the app');
        $this->info('   3. To test: Clear browser cache and reload');

        return 0;
    }

    /**
     * Get current version from service worker
     */
    private function getCurrentVersion()
    {
        $swPath = public_path('service-worker.js');
        if (File::exists($swPath)) {
            $content = File::get($swPath);
            if (preg_match("/const APP_VERSION = '([^']+)';/", $content, $matches)) {
                return $matches[1];
            }
        }
        return 'unknown';
    }

    /**
     * Generate version number based on date and time
     */
    private function generateVersion()
    {
        // Format: YYYY.MM.DD.HH (e.g., 2026.04.01.14)
        return date('Y.m.d.H');
    }

    /**
     * Update service-worker.js file
     */
    private function updateServiceWorker($oldVersion, $newVersion)
    {
        $swPath = public_path('service-worker.js');
        if (!File::exists($swPath)) {
            $this->error('❌ service-worker.js not found!');
            return false;
        }

        $content = File::get($swPath);

        // Update APP_VERSION
        $content = preg_replace(
            "/const APP_VERSION = '{$oldVersion}';/",
            "const APP_VERSION = '{$newVersion}';",
            $content
        );

        // Update CACHE_NAME (it uses APP_VERSION)
        $content = preg_replace(
            "/const CACHE_NAME = `shuleapp-cache-{$oldVersion}`;/",
            "const CACHE_NAME = `shuleapp-cache-{$newVersion}`;",
            $content
        );

        File::put($swPath, $content);
        return true;
    }

    /**
     * Update manifest.json file
     */
    private function updateManifest($oldVersion, $newVersion)
    {
        $manifestPath = public_path('manifest.json');
        if (!File::exists($manifestPath)) {
            $this->error('❌ manifest.json not found!');
            return false;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest) {
            $this->error('❌ Invalid manifest.json!');
            return false;
        }

        // Update start_url
        $manifest['start_url'] = "/?v={$newVersion}";

        // Update id
        $manifest['id'] = "shuleapp-{$newVersion}";

        // Update icon URLs
        if (isset($manifest['icons'])) {
            foreach ($manifest['icons'] as &$icon) {
                if (isset($icon['src'])) {
                    $icon['src'] = preg_replace('/\?v=[^"]+/', "?v={$newVersion}", $icon['src']);
                }
            }
        }

        // Write back
        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return true;
    }
}
