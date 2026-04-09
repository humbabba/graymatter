<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Project;
use App\Models\Trash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupImagesCommand extends Command
{
    protected $signature = 'images:cleanup
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Delete orphaned images not referenced by any project, setting, or trashed item';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $files = $disk->files('project-images');

        if (empty($files)) {
            $this->info('No images found.');
            return self::SUCCESS;
        }

        $referenced = $this->getReferencedImages();
        $orphaned = array_filter($files, fn ($file) => !in_array(basename($file), $referenced));

        if (empty($orphaned)) {
            $this->info('No orphaned images found.');
            return self::SUCCESS;
        }

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('[Dry run] Would delete ' . count($orphaned) . ' orphaned image(s):');
            foreach ($orphaned as $file) {
                $this->line("  - {$file}");
            }
            return self::SUCCESS;
        }

        foreach ($orphaned as $file) {
            $disk->delete($file);
        }

        $this->info('Deleted ' . count($orphaned) . ' orphaned image(s).');

        return self::SUCCESS;
    }

    protected function getReferencedImages(): array
    {
        $referenced = [];

        // Project content
        Project::whereNotNull('content')->chunk(100, function ($projects) use (&$referenced) {
            foreach ($projects as $project) {
                $referenced = array_merge($referenced, $this->extractImages($project->content));
            }
        });

        // News setting (rich text on home page)
        $news = AppSetting::where('key', 'news')->value('value');
        if ($news) {
            $referenced = array_merge($referenced, $this->extractImages($news));
        }

        // Trashed items (may be restored)
        Trash::chunk(100, function ($items) use (&$referenced) {
            foreach ($items as $item) {
                $content = $item->data['content'] ?? null;
                if ($content) {
                    $referenced = array_merge($referenced, $this->extractImages($content));
                }
            }
        });

        return array_unique($referenced);
    }

    protected function extractImages(string $html): array
    {
        preg_match_all('/\/storage\/project-images\/([^\s"\'<>]+)/', $html, $matches);

        return $matches[1] ?? [];
    }
}
