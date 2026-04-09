<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Project;
use Illuminate\Console\Command;

class MigrateRichContent extends Command
{
    protected $signature = 'content:migrate-from-trix';
    protected $description = 'Migrate stored HTML from Trix format (align-center tags, data-trix attributes) to Tip Tap format';

    public function handle(): int
    {
        $this->info('Migrating rich content from Trix to Tip Tap format...');

        // Migrate project content
        $projects = Project::whereNotNull('content')->where('content', '!=', '')->get();
        $migrated = 0;

        foreach ($projects as $project) {
            $original = $project->content;
            $cleaned = $this->migrateHtml($original);

            if ($cleaned !== $original) {
                $project->timestamps = false; // Don't update updated_at
                $project->content = $cleaned;
                $project->saveQuietly(); // Skip model events
                $migrated++;
                $this->line("  Project #{$project->id}: migrated");
            }
        }

        $this->info("Migrated {$migrated} project(s).");

        // Migrate app settings (news, etc.)
        $settings = AppSetting::where('type', 'richtext')->get();
        foreach ($settings as $setting) {
            if (empty($setting->value)) continue;

            $original = $setting->value;
            $cleaned = $this->migrateHtml($original);

            if ($cleaned !== $original) {
                $setting->value = $cleaned;
                $setting->save();
                $this->line("  Setting '{$setting->key}': migrated");
            }
        }

        AppSetting::clearCache();
        $this->info('Done.');

        return self::SUCCESS;
    }

    protected function migrateHtml(string $html): string
    {
        // Handle <h1><align-center>text</align-center></h1> → <h1 style="text-align: center">text</h1>
        $html = preg_replace(
            '/<(h[1-6])([^>]*)><align-center>(.*?)<\/align-center><\/\1>/s',
            '<$1$2 style="text-align: center">$3</$1>',
            $html
        );
        $html = preg_replace(
            '/<(h[1-6])([^>]*)><align-right>(.*?)<\/align-right><\/\1>/s',
            '<$1$2 style="text-align: right">$3</$1>',
            $html
        );

        // Replace remaining <align-center>...</align-center> — add style to child block elements
        $html = preg_replace_callback(
            '/<align-center>(.*?)<\/align-center>/s',
            fn($m) => $this->addAlignmentToChildren($m[1], 'center'),
            $html
        );

        $html = preg_replace_callback(
            '/<align-right>(.*?)<\/align-right>/s',
            fn($m) => $this->addAlignmentToChildren($m[1], 'right'),
            $html
        );

        // Strip data-trix-* attributes
        $html = preg_replace('/\s+data-trix-[a-z-]+="[^"]*"/i', '', $html);
        $html = preg_replace("/\s+data-trix-[a-z-]+='[^']*'/i", '', $html);

        // Convert <figure ...><a href="..."><img ...></a>...</figure> to plain <img>
        $html = preg_replace_callback(
            '/<figure[^>]*>.*?<img\s([^>]*)>.*?<\/figure>/s',
            function ($m) {
                return '<img ' . $m[1] . '>';
            },
            $html
        );

        // Clean up empty class attributes
        $html = preg_replace('/\s+class=""/i', '', $html);

        return $html;
    }

    protected function addAlignmentToChildren(string $inner, string $alignment): string
    {
        // If the inner content starts with a block element, add style to it
        $result = preg_replace_callback(
            '/<(p|h[1-6]|div|blockquote)([\s>])/i',
            function ($m) use ($alignment) {
                $tag = $m[1];
                $after = $m[2];
                if ($after === '>') {
                    return "<{$tag} style=\"text-align: {$alignment}\">";
                }
                return "<{$tag} style=\"text-align: {$alignment}\"{$after}";
            },
            $inner
        );

        // If no block element was found (bare text/br), wrap in a styled paragraph
        if ($result === $inner && !preg_match('/<(p|h[1-6]|div|blockquote)/i', $inner)) {
            $result = "<p style=\"text-align: {$alignment}\">{$inner}</p>";
        }

        return $result;
    }
}
