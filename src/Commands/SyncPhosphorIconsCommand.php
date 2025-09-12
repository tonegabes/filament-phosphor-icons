<?php

declare(strict_types=1);

namespace ToneGabes\Filament\Icons\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Command to synchronize Phosphor icons with the enum
 */
class SyncPhosphorIconsCommand extends Command
{
    /**
     * Command name and signature
     */
    protected $signature = 'phosphor:sync {--dry-run : Run without modifying files}';

    /**
     * Command description
     */
    protected $description = 'Synchronize icons from blade-phosphor-icons package with Phosphor enum';

    /**
     * Path to blade-phosphor-icons SVGs
     */
    private const SVG_PATH = 'vendor/codeat3/blade-phosphor-icons/resources/svg';

    /**
     * Path to Phosphor enum
     */
    private const ENUM_PATH = 'vendor/tonegabes/filament-phosphor-icons/src/Enums/Phosphor.php';

    /**
     * Execute the command
     */
    public function handle(): int
    {
        $this->info('🔄 Starting Phosphor icons synchronization...');

        $basePath = $this->getBasePath();

        if (!File::isDirectory($basePath . '/' . self::SVG_PATH)) {
            $this->error('❌ SVG directory not found: ' . self::SVG_PATH);
            $this->error('Make sure the codeat3/blade-phosphor-icons package is installed.');
            return Command::FAILURE;
        }

        if (!File::exists($basePath . '/' . self::ENUM_PATH)) {
            $this->error('❌ Enum file not found: ' . self::ENUM_PATH);
            return Command::FAILURE;
        }

        try {
            $svgIcons = $this->getSvgIcons();
            $this->info("📁 Found {$svgIcons->count()} SVG icons");

            $enumIcons = $this->getEnumIcons();
            $this->info("📋 Found {$enumIcons->count()} icons in enum");

            $missingIcons = $svgIcons->diff($enumIcons);

            if ($missingIcons->isEmpty()) {
                $this->info('✅ All icons are already synchronized!');
                return Command::SUCCESS;
            }

            $this->warn("⚠️  Found {$missingIcons->count()} missing icons in enum:");

            $preview = $missingIcons->take(10);
            foreach ($preview as $icon) {
                $this->line("  • {$icon}");
            }

            if ($missingIcons->count() > 10) {
                $this->line("  ... and " . ($missingIcons->count() - 10) . " more icons");
            }

            if ($this->option('dry-run')) {
                $this->info('🔍 Dry-run mode: no changes were made');
                return Command::SUCCESS;
            }

            if (!$this->confirm('Do you want to add these icons to the enum?')) {
                $this->info('❌ Operation cancelled');
                return Command::SUCCESS;
            }

            $this->addIconsToEnum($missingIcons);

            $this->info('✅ Synchronization completed successfully!');
            $this->info("📝 Added {$missingIcons->count()} new icons to enum");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error during synchronization: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get the list of icons from SVG files
     */
    private function getSvgIcons(): Collection
    {
        $basePath = $this->getBasePath();
        $svgFiles = File::files($basePath . '/' . self::SVG_PATH);

        return collect($svgFiles)
            ->map(fn ($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->sort()
            ->values();
    }

    /**
     * Get the list of icons from current enum
     */
    private function getEnumIcons(): Collection
    {
        $basePath = $this->getBasePath();
        $enumContent = File::get($basePath . '/' . self::ENUM_PATH);

        preg_match_all("/case\s+\w+\s+=\s+'([^']+)'/", $enumContent, $matches);

        return collect($matches[1])
            ->sort()
            ->values();
    }

    /**
     * Add missing icons to enum
     */
    private function addIconsToEnum(Collection $missingIcons): void
    {
        $basePath = $this->getBasePath();
        $enumContent = File::get($basePath . '/' . self::ENUM_PATH);

        $newCases = $missingIcons
            ->map(fn ($icon) => $this->generateEnumCase($icon))
            ->join("\n    ");

        $pattern = '/(\s+case\s+\w+\s+=\s+\'[^\']+\';\s*)(\s*\/\*\*)/s';

        if (preg_match($pattern, $enumContent)) {
            $enumContent = preg_replace(
                $pattern,
                "$1    $newCases\n$2",
                $enumContent
            );
        } else {
            $pattern = '/(\s+case\s+\w+\s+=\s+\'[^\']+\';\s*)(\s*public\s+function)/s';

            if (preg_match($pattern, $enumContent)) {
                $enumContent = preg_replace(
                    $pattern,
                    "$1    $newCases\n\n$2",
                    $enumContent
                );
            } else {
                $enumContent = str_replace(
                    "\n}",
                    "\n    $newCases\n}",
                    $enumContent
                );
            }
        }

        File::put($basePath . '/' . self::ENUM_PATH, $enumContent);
    }

    /**
     * Get project base path
     */
    private function getBasePath(): string
    {
        if ($this->laravel && method_exists($this->laravel, 'basePath')) {
            return $this->laravel->basePath();
        }

        $currentDir = getcwd() ?: __DIR__;

        while ($currentDir !== dirname($currentDir)) {
            if (file_exists($currentDir . '/composer.json')) {
                return $currentDir;
            }
            $currentDir = dirname($currentDir);
        }

        return getcwd() ?: __DIR__;
    }

    /**
     * Generate an enum case for an icon
     */
    private function generateEnumCase(string $iconName): string
    {
        $caseName = Str::of($iconName)
            ->kebab()
            ->split('/-/')
            ->map(fn ($part) => Str::ucfirst($part))
            ->join('');

        return "case {$caseName} = '{$iconName}';";
    }
}
