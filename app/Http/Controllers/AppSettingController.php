<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\NavItem;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        // JSON checkbox groups won't appear in input when nothing is checked
        $jsonSettings = AppSetting::where('type', 'json')->pluck('key');
        foreach ($jsonSettings as $key) {
            if (!array_key_exists($key, $settings)) {
                $settings[$key] = [];
            }
        }

        foreach ($settings as $key => $value) {
            $setting = AppSetting::where('key', $key)->first();

            if ($setting) {
                if ($setting->type === 'boolean') {
                    $value = isset($value) && $value ? 'true' : 'false';
                } elseif ($setting->type === 'json') {
                    $value = json_encode(is_array($value) ? $value : []);
                }

                $setting->value = $value;
                $setting->save();
            }
        }

        AppSetting::clearCache();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Settings have been saved.']);
        }

        return redirect()->route('settings.index')
            ->with('status', 'Settings have been saved.');
    }

    public function navIndex()
    {
        $navItems = NavItem::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($item) => [
                'label' => $item->label,
                'url' => $item->url,
                'roles' => $item->roles ?? [],
                'children' => $item->children->map(fn($c) => [
                    'label' => $c->label,
                    'url' => $c->url,
                    'roles' => $c->roles ?? [],
                ])->toArray(),
            ]);

        $roles = \App\Models\Role::orderBy('name')->pluck('name')->prepend('guest');

        $manageableModels = collect(\App\Console\Commands\SyncPermissions::getManageableModels())
            ->map(fn($class) => [
                'label' => $class::permissionLabel(),
                'url' => '/' . $class::permissionPrefix(),
            ])->values();

        return view('settings.nav', compact('navItems', 'roles', 'manageableModels'));
    }

    public function updateNav(Request $request)
    {
        $items = $request->input('items', []);

        NavItem::query()->delete();

        $sortOrder = 1;
        foreach ($items as $item) {
            $parent = NavItem::create([
                'label' => $item['label'],
                'url' => $item['url'] ?? '#',
                'sort_order' => $sortOrder++,
                'roles' => !empty($item['roles']) ? $item['roles'] : null,
            ]);

            if (!empty($item['children'])) {
                $childSort = 1;
                foreach ($item['children'] as $child) {
                    NavItem::create([
                        'label' => $child['label'],
                        'url' => $child['url'] ?? '#',
                        'parent_id' => $parent->id,
                        'sort_order' => $childSort++,
                        'roles' => !empty($child['roles']) ? $child['roles'] : null,
                    ]);
                }
            }
        }

        NavItem::clearCache();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Navigation updated.']);
        }

        return redirect()->route('settings.index')->with('status', 'Navigation updated.');
    }
}
