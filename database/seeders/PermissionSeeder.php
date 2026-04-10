<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\NavItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create all permissions
        $this->createPermissionsForModel('users', 'Users');
        $this->createPermissionsForModel('roles', 'Roles');
        $this->createPermissionsForModel('projects', 'Projects');
        Permission::create(['name' => 'trash.view', 'description' => 'View trash']);
        Permission::create(['name' => 'trash.restore', 'description' => 'Restore trash']);
        Permission::create(['name' => 'trash.delete', 'description' => 'Delete trash']);
        Permission::create(['name' => 'settings.manage', 'description' => 'Manage settings']);
        Permission::create(['name' => 'activity-logs.view', 'description' => 'View activity logs']);

        // Admin role — all permissions
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access.',
        ]);
        $adminRole->permissions()->attach(Permission::all());

        // User role — can create projects
        $userRole = Role::create([
            'name' => 'user',
            'description' => 'Standard user with project access.',
        ]);
        $userRole->permissions()->attach(
            Permission::where('name', 'projects.create')->pluck('id')
        );

        // Admin can assign all roles (including itself)
        $adminRole->assignableRoles()->attach(Role::all());

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@example.com')],
            ['name' => env('SEED_ADMIN_NAME', 'Example Admin')]
        );
        $adminUser->roles()->attach($adminRole);

        // Seed default app settings
        AppSetting::create([
            'key' => 'trash_retention_days',
            'value' => '30',
            'type' => 'integer',
            'group' => 'trash',
            'description' => 'Number of days to retain items in trash before automatic cleanup',
        ]);
        AppSetting::create([
            'key' => 'trash_auto_cleanup_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'group' => 'trash',
            'description' => 'Enable automatic cleanup of old trash items',
        ]);
        AppSetting::create([
            'key' => 'news',
            'value' => '',
            'type' => 'richtext',
            'group' => 'general',
            'description' => 'News or announcements to display to users (supports HTML)',
        ]);

        AppSetting::create([
            'key' => 'auth_mode',
            'value' => 'code',
            'type' => 'select',
            'options' => [
                'code' => 'Email code',
                'password' => 'Password',
                'password_2fa' => 'Password + email 2FA',
            ],
            'group' => 'auth',
            'description' => 'Authentication method used for login and registration',
        ]);

        AppSetting::create([
            'key' => 'allow_registration',
            'value' => 'true',
            'type' => 'boolean',
            'group' => 'auth',
            'description' => 'Allow new users to register accounts',
        ]);

        AppSetting::create([
            'key' => 'publication_year',
            'value' => '0000',
            'type' => 'string',
            'group' => 'general',
            'description' => 'Year of first publication for the copyright notice (e.g. 2024). Set to 0000 to show current year only.',
        ]);

        // Appearance settings
        AppSetting::create([
            'key' => 'theme_accent',
            'value' => 'grayscale',
            'type' => 'select',
            'options' => ['grayscale' => 'Grayscale', 'green' => 'Green', 'blue' => 'Blue', 'amber' => 'Amber', 'rose' => 'Rose'],
            'group' => 'appearance',
            'description' => 'Accent color theme applied across the interface',
            'sort_order' => 10,
        ]);
        AppSetting::create([
            'key' => 'link_color',
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
        ]);
        AppSetting::create([
            'key' => 'link_style',
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
        ]);
        AppSetting::create([
            'key' => 'link_hover_color',
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
        ]);
        AppSetting::create([
            'key' => 'link_hover_style',
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
        ]);
        AppSetting::create([
            'key' => 'theme_font',
            'value' => 'inter',
            'type' => 'select',
            'options' => [
                'system' => 'System default',
                'inter' => 'Inter / Share Tech Mono',
                'roboto-condensed' => 'Roboto Condensed / Source Sans 3',
                'titillium' => 'Titillium Web / Nunito Sans',
                'saira-stencil' => 'Saira Stencil One / Saira',
                'bitcount' => 'Bitcount Grid Double / Space Grotesk',
                'oswald' => 'Oswald / Lato',
                'ubuntu' => 'Ubuntu / Ubuntu Sans',
            ],
            'group' => 'appearance',
            'description' => 'Font pairing used for body text and display elements',
            'sort_order' => 20,
        ]);

        // Seed default navigation
        $projects = NavItem::create(['label' => 'Projects', 'url' => '/projects', 'sort_order' => 1, 'roles' => ['guest', 'admin', 'user']]);

        $utils = NavItem::create(['label' => 'Utils', 'url' => '#', 'sort_order' => 2]);
        NavItem::create(['label' => 'Settings', 'url' => '/settings', 'parent_id' => $utils->id, 'sort_order' => 1, 'roles' => ['admin']]);
        NavItem::create(['label' => 'Users', 'url' => '/users', 'parent_id' => $utils->id, 'sort_order' => 2, 'roles' => ['guest', 'admin', 'user']]);
        NavItem::create(['label' => 'Roles', 'url' => '/roles', 'parent_id' => $utils->id, 'sort_order' => 3, 'roles' => ['guest', 'admin', 'user']]);
        NavItem::create(['label' => 'Navigation', 'url' => '/settings/nav', 'parent_id' => $utils->id, 'sort_order' => 4, 'roles' => ['admin']]);
        NavItem::create(['label' => 'Activity log', 'url' => '/activity-logs', 'parent_id' => $utils->id, 'sort_order' => 5, 'roles' => ['admin', 'guest']]);
        NavItem::create(['label' => 'Trash', 'url' => '/trash', 'parent_id' => $utils->id, 'sort_order' => 6, 'roles' => ['guest', 'admin', 'user']]);

        NavItem::clearCache();
    }

    protected function createPermissionsForModel(string $model, string $label): void
    {
        $actions = [
            'view' => "View {$label}",
            'create' => "Create {$label}",
            'edit' => "Edit {$label}",
            'delete' => "Delete {$label}",
        ];

        foreach ($actions as $action => $description) {
            Permission::create([
                'name' => "{$model}.{$action}",
                'description' => $description,
            ]);
        }
    }
}
