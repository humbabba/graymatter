<?php

use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('app_settings', 'sort_order')) {
            Schema::table('app_settings', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('description');
            });
        }

        AppSetting::firstOrCreate(
            ['key' => 'link_color'],
            [
                'value' => 'accent',
                'type' => 'select',
                'options' => [
                    'accent' => 'Accent (default)',
                    'grayscale' => 'Grayscale',
                    'green' => 'Green',
                    'blue' => 'Blue',
                    'amber' => 'Amber',
                    'rose' => 'Rose',
                ],
                'group' => 'appearance',
                'description' => 'Color used for inline links. Defaults to the current accent color.',
                'sort_order' => 30,
            ]
        );

        AppSetting::firstOrCreate(
            ['key' => 'link_style'],
            [
                'value' => json_encode([]),
                'type' => 'json',
                'options' => [
                    'bold' => 'Bold',
                    'underline' => 'Underline',
                    'italic' => 'Italic',
                ],
                'group' => 'appearance',
                'description' => 'Typographic styles applied to inline links.',
                'sort_order' => 40,
            ]
        );

        AppSetting::firstOrCreate(
            ['key' => 'link_hover_color'],
            [
                'value' => 'auto',
                'type' => 'select',
                'options' => [
                    'auto' => 'Auto (darken link color)',
                    'accent' => 'Accent',
                    'grayscale' => 'Grayscale',
                    'green' => 'Green',
                    'blue' => 'Blue',
                    'amber' => 'Amber',
                    'rose' => 'Rose',
                ],
                'group' => 'appearance',
                'description' => 'Color used for links on hover. Defaults to a slightly darker version of the link color.',
                'sort_order' => 50,
            ]
        );

        AppSetting::firstOrCreate(
            ['key' => 'link_hover_style'],
            [
                'value' => json_encode([]),
                'type' => 'json',
                'options' => [
                    'bold' => 'Bold',
                    'underline' => 'Underline',
                    'italic' => 'Italic',
                ],
                'group' => 'appearance',
                'description' => 'Typographic styles applied to inline links on hover.',
                'sort_order' => 60,
            ]
        );

        AppSetting::where('key', 'theme_accent')->update(['sort_order' => 10]);
        AppSetting::where('key', 'theme_font')->update(['sort_order' => 20]);

        AppSetting::clearCache();
    }

    public function down(): void
    {
        AppSetting::whereIn('key', ['link_color', 'link_style', 'link_hover_color', 'link_hover_style'])->delete();
        AppSetting::clearCache();
    }
};
