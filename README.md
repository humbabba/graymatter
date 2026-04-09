# Graymatter

A Laravel application template with role-based access control, configurable authentication, a rich text editor, theme system, activity logging, and soft-delete trash management. Designed as a starting point for building content-driven web applications.

## Requirements

- PHP 8.2+
- MySQL 8.0+ or SQLite
- Node.js 18+
- Composer

## Installation

```bash
git clone https://github.com/humbabba/graymatter.git
cd graymatter
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with database credentials, then:

```bash
php artisan migrate --seed
php artisan storage:link
mkdir -p storage/app/public/project-images
npm run build
php artisan serve
```

The `storage:link` command creates the `public/storage` symlink so uploaded files are web-accessible. The `mkdir` creates the image upload directory. On shared hosts where `storage:link` doesn't work, create the symlink manually:

```bash
ln -s /absolute/path/to/storage/app/public /absolute/path/to/public/storage
```

The default admin account is `admin@example.com`. With the default auth mode (email code), enter this email on the login page. In local development, the 6-digit code is displayed on-screen instead of emailed.

## Features

### Authentication

Three configurable modes, selectable in Settings:

- **Email code** (default): Enter your email, receive a 6-digit code, enter it to log in
- **Password:** Standard email and password login
- **Password + 2FA:** Password login followed by an emailed 6-digit verification code

When switching from email code to a password mode, existing users are prompted to set a password on their next login. Registration can be disabled entirely via settings. Codes expire after 10 minutes and are stored hashed.

### Roles and permissions

Users are assigned roles, and roles have permissions. Two roles are seeded by default:

- **admin:** Full access to all features
- **user:** Can create projects

Permissions follow the pattern `{model}.{action}` (e.g., `projects.view`, `users.edit`). The Manageable trait on a model auto-generates view, create, edit, and delete permissions via `php artisan permissions:sync`.

Roles can be configured to assign other roles, creating an assignment hierarchy (e.g., admins can assign any role, but editors can only assign the "user" role).

### Guest access

The navigation manager includes a virtual "guest" role. Checking "guest" on a nav item makes its corresponding route and model accessible to unauthenticated visitors. This is driven entirely from the nav configuration: No code changes needed to make a section public.

### Navigation manager

Admins can configure the main navigation at `/settings/nav`:

- Add, remove, and reorder top-level items and children
- Assign role visibility per item (including the "guest" virtual role)
- Indent items to nest them under a parent, or outdent to promote them
- Add all manageable models as nav items in one click

### Rich text editor

Content editing uses TipTap (ProseMirror-based). Features include:

- Text formatting (bold, italic, underline, strikethrough, inline code)
- Headings (H1 through H6)
- Text alignment (left, center, right) via inline styles on real HTML elements
- Bullet and ordered lists, blockquotes, code blocks
- Link insertion (text and images)
- Image upload with drag-to-resize handles
- Undo/redo with keyboard shortcuts
- AJAX save with Ctrl+S and a visual save indicator

Images are uploaded to `storage/app/public/project-images` (max 5 MB). Content is sanitized server-side, stripping event handlers and dangerous URLs while preserving formatting.

### Themes

**Accent colors:** Grayscale (default), Green, Blue, Amber, Rose: Configurable in settings, applied globally via CSS custom properties.

**Font pairings** (display / body):

| Key | Display font | Body font |
|-----|-------------|-----------|
| `inter` | Share Tech Mono | Inter |
| `roboto-condensed` | Roboto Condensed | Source Sans 3 |
| `titillium` | Titillium Web | Nunito Sans |
| `saira-stencil` | Saira Stencil One | Saira |
| `bitcount` | Bitcount Grid Double | Space Grotesk |
| `oswald` | Oswald | Lato |
| `ubuntu` | Ubuntu | Ubuntu Sans |

All fonts are hosted locally. A system default option uses the browser's native font stack.

**Dark/light mode:** Toggled per user via the nav bar button, persisted to the user's profile and localStorage.

### Typography and styling

All typography is styled globally in `@layer base` in `app.css`: Headings, paragraphs, lists, blockquotes, code blocks, images, and links all have consistent font sizes, line heights, and margins regardless of context. No wrapper class is needed to style rendered HTML.

Block elements (p, ul, ol, blockquote, pre) have bottom margins. Headings have a smaller top margin and a bottom margin. All margins are suppressed on `:last-child` to avoid trailing whitespace. If a UI element (navigation, dropdowns) uses `<ul>` or `<ol>` for layout, reset the global list styles with Tailwind's `list-none pl-0` utilities.

### Activity logging

Models using the Loggable trait automatically record create, update, and delete actions with full change tracking (old and new values, including nested arrays). The activity log is browsable at `/activity-logs` with filtering by model type, action, user, and date range.

### Trash and soft delete

Models using the Trashable trait move to a trash archive on deletion instead of being permanently removed. The trash view at `/trash` supports:

- Browsing and searching deleted items
- Restoring items (including their relationships)
- Permanent deletion
- Batch cleanup of expired items

Retention is configurable (default 30 days). Automatic cleanup can be enabled in settings. See [Scheduled tasks](#scheduled-tasks) below.

### App settings

A key-value settings system with typed values (string, integer, boolean, select, richtext, json). Settings are grouped by category (auth, general, appearance, trash) and editable at `/settings`. Values are cached for one hour.

### Admin notifications

Admins can opt into email notifications on their profile page:

- **New users:** Emailed when a new user completes their first login
- **New model instances:** Per-model toggles (e.g., notify when a new project is created)

A pulsing indicator appears on the Users nav item when new users have registered since the admin last visited the users list.

### Projects

The default content model. Projects have a name, description, and rich text content with image support. Features include duplication, creator attribution, and configurable public visibility via the nav manager.

## Development

```bash
npm run dev     # Start Vite dev server with hot reload
npm run build   # Production build
```

### Key directories

```
app/Models/          # Eloquent models with trait-based features
app/Traits/          # Loggable, Manageable, Trashable, Copyable, Searchable, HasRoles
app/Http/Controllers/Auth/  # Authentication (3 modes)
resources/js/        # Alpine.js components, TipTap editor, AJAX save
resources/css/       # Tailwind CSS with theme variables and font faces
public/fonts/        # Locally hosted font files (woff2)
database/seeders/    # Default roles, permissions, settings, nav items
```

### Adding a new model

1. Create the model with the desired traits (`Manageable`, `Loggable`, `Trashable`, `Searchable`, `Copyable`)
2. Run `php artisan permissions:sync` to generate CRUD permissions
3. Create the controller, views, and routes
4. Add a nav item via the navigation manager

The Manageable trait auto-generates permissions and provides `canView()` / `canManage()` authorization helpers. The model will automatically appear in the admin notification preferences.

### Removing a model

When removing a model, delete its permissions in the migration's `down` method (or in a dedicated migration). Permissions live in the database, so removing the model code alone will leave orphaned permission rows that still appear in the roles editor. For example:

```php
public function down(): void
{
    // Remove permissions for the deleted model
    \App\Models\Permission::where('name', 'like', 'modelname.%')->delete();

    Schema::dropIfExists('modelnames');
}
```

### Scheduled tasks

The application defines two scheduled commands in `routes/console.php`. These require Laravel's scheduler to be running (`php artisan schedule:work` in development, or a cron entry in production).

| Time | Command | Description |
|------|---------|-------------|
| 02:00 | `php artisan trash:cleanup` | Permanently deletes trash items older than the configured retention period (default 30 days). Respects the `trash_auto_cleanup_enabled` and `trash_retention_days` settings. |
| 02:30 | `php artisan images:cleanup` | Deletes orphaned images from `storage/app/public/project-images/` that are not referenced in any project content, the news setting, or trashed item data. Runs after trash cleanup so images from freshly purged trash are caught in the same cycle. |

Both commands support `--dry-run` to preview what would be deleted. `trash:cleanup` also accepts `--days=N` to override the retention setting.

## License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
