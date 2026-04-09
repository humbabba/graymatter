<x-layouts.app>
    <div class="p-4 md:p-8 max-w-4xl mx-auto">
        <h1 class="text-3xl mb-8" style="font-family: var(--font-display);">User manual</h1>

        <div class="space-y-12">

            {{-- TABLE OF CONTENTS --}}
            <nav class="bg-graymatter-panel-light rounded-sm p-6">
                <h2 class="text-xl mb-4" style="font-family: var(--font-display);">Contents</h2>
                <ul class="space-y-1 text-sm">
                    <li><a href="#getting-started" class="text-graymatter-teal hover:text-graymatter-lime">Getting started</a></li>
                    <li><a href="#projects" class="text-graymatter-teal hover:text-graymatter-lime">Projects</a></li>
                    <li><a href="#editor" class="text-graymatter-teal hover:text-graymatter-lime">Rich text editor</a></li>
                    <li><a href="#users" class="text-graymatter-teal hover:text-graymatter-lime">Users</a></li>
                    <li><a href="#roles" class="text-graymatter-teal hover:text-graymatter-lime">Roles and permissions</a></li>
                    <li><a href="#navigation" class="text-graymatter-teal hover:text-graymatter-lime">Navigation manager</a></li>
                    <li><a href="#settings" class="text-graymatter-teal hover:text-graymatter-lime">Settings</a></li>
                    <li><a href="#themes" class="text-graymatter-teal hover:text-graymatter-lime">Themes and appearance</a></li>
                    <li><a href="#activity-log" class="text-graymatter-teal hover:text-graymatter-lime">Activity log</a></li>
                    <li><a href="#trash" class="text-graymatter-teal hover:text-graymatter-lime">Trash</a></li>
                    <li><a href="#keyboard" class="text-graymatter-teal hover:text-graymatter-lime">Keyboard shortcuts</a></li>
                </ul>
            </nav>

            {{-- GETTING STARTED --}}
            <section id="getting-started">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Getting started</h2>
                <p>After registering or receiving login credentials, sign in using the method configured by your administrator:</p>
                <ul>
                    <li><strong>Email code:</strong> Enter your email address and you'll receive a 6-digit verification code. Enter it on the next screen to log in. The code expires after 10 minutes.</li>
                    <li><strong>Password:</strong> Enter your email and password.</li>
                    <li><strong>Password + 2FA:</strong> Enter your email and password, then enter the 6-digit code sent to your email.</li>
                </ul>
                <p>If the system switches from email code to a password mode and you don't have a password yet, you'll be prompted to set one using a verification code.</p>
                <p>Once logged in, you'll see the home page with any news or announcements. Use the navigation bar at the top to access different sections.</p>
            </section>

            {{-- PROJECTS --}}
            <section id="projects">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Projects</h2>
                <p>Projects are the main content type. Each project has a name, a short description, and rich text content that supports formatting, images, and links.</p>

                <h3 class="border-b">Creating a project</h3>
                <p>Click <strong>Add project</strong> on the projects page. Fill in the name and description, then use the rich text editor for the content. Click <strong>Create project</strong> or press <strong>Ctrl+S</strong> to save.</p>

                <h3 class="border-b">Editing a project</h3>
                <p>Open a project and click <strong>Edit</strong>. Changes are saved via AJAX: A save indicator appears at the top right of the page. It shows:</p>
                <ul>
                    <li><strong>Unsaved changes</strong> (red, pulsing): You have unsaved edits</li>
                    <li><strong>Saving...</strong> (green): Save in progress</li>
                    <li><strong>Saved!</strong> (green): Changes saved successfully</li>
                </ul>
                <p>You can also click the indicator or press <strong>Ctrl+S</strong> to save at any time.</p>

                <h3 class="border-b">Copying a project</h3>
                <p>Click <strong>Copy</strong> on the projects list to duplicate a project. The copy is created with "(copy)" appended to the name and assigned to you as the creator.</p>

                <h3 class="border-b">Filtering projects</h3>
                <p>Use the search bar to filter by name or description. Use the date filters to narrow by creation date. Click <strong>My projects</strong> to see only your own.</p>
            </section>

            {{-- EDITOR --}}
            <section id="editor">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Rich text editor</h2>
                <p>The editor toolbar provides formatting options grouped by function:</p>

                <h3 class="border-b">Text formatting</h3>
                <p><strong>Bold</strong>, <em>italic</em>, <u>underline</u>, <del>strikethrough</del>, and <code>inline code</code>. Select text and click the corresponding toolbar button, or use keyboard shortcuts (Ctrl+B, Ctrl+I, Ctrl+U).</p>

                <h3 class="border-b">Headings</h3>
                <p>Six heading levels (H1 through H6). Click a heading button to toggle the current paragraph to that heading level. Click again to revert to a normal paragraph.</p>

                <h3 class="border-b">Alignment</h3>
                <p>Align text left, center, or right. Select the paragraph or heading and click an alignment button. Alignment is stored as an inline style on the element.</p>

                <h3 class="border-b">Lists and blocks</h3>
                <ul>
                    <li><strong>Bullet list</strong> and <strong>ordered list</strong></li>
                    <li><strong>Blockquote:</strong> Indented, styled quote block</li>
                    <li><strong>Code block:</strong> Monospace block for code snippets</li>
                </ul>

                <h3 class="border-b">Links</h3>
                <p>Select text and click the link button to add a URL. Click again on linked text to change or remove the link. To link an image, select the image first, then click the link button.</p>

                <h3 class="border-b">Images</h3>
                <p>Click the image button in the toolbar to upload an image (maximum 5 MB). Once inserted:</p>
                <ul>
                    <li><strong>Resize:</strong> Drag the handles at the corners or edges of the image</li>
                    <li><strong>Align:</strong> Select the image and use the alignment buttons</li>
                    <li><strong>Link:</strong> Select the image and click the link button</li>
                </ul>

                <h3 class="border-b">Undo and redo</h3>
                <p>Use the toolbar buttons or <strong>Ctrl+Z</strong> / <strong>Ctrl+Shift+Z</strong>.</p>
            </section>

            {{-- USERS --}}
            <section id="users">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Users</h2>
                <p>The users section lists all registered accounts. Depending on your permissions, you may be able to:</p>
                <ul>
                    <li><strong>View</strong> user profiles (name, role, creation date, last login)</li>
                    <li><strong>Create</strong> new users and assign them a role</li>
                    <li><strong>Edit</strong> user details and change their role</li>
                    <li><strong>Delete</strong> users (moves them to trash)</li>
                </ul>

                <h3 class="border-b">Your profile</h3>
                <p>Click your avatar in the navigation bar to view your profile, then click <strong>Edit</strong> to change your settings:</p>
                <ul>
                    <li><strong>Starting view:</strong> The page you see after logging in (default is home)</li>
                    <li><strong>Theme:</strong> Light or dark mode</li>
                    <li><strong>Password:</strong> Change your password (when using password authentication)</li>
                </ul>

                <h3 class="border-b">Admin notifications</h3>
                <p>Administrators see additional options on their profile:</p>
                <ul>
                    <li><strong>Notify on new users:</strong> Receive an email when a new user completes their first login</li>
                    <li><strong>Notify on new content:</strong> Per-model toggles for email alerts when new items are created (e.g., new projects, new roles)</li>
                </ul>
                <p>A pulsing indicator appears on the Users nav item when new users have registered since your last visit to the users list.</p>
            </section>

            {{-- ROLES --}}
            <section id="roles">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Roles and permissions</h2>
                <p>Access control is managed through roles and permissions. Each user is assigned one role, and each role has a set of permissions.</p>

                <h3 class="border-b">Permissions</h3>
                <p>Permissions follow the pattern <code>model.action</code>:</p>
                <ul>
                    <li><strong>view:</strong> See the list and detail pages</li>
                    <li><strong>create:</strong> Add new items</li>
                    <li><strong>edit:</strong> Modify existing items</li>
                    <li><strong>delete:</strong> Remove items (to trash)</li>
                </ul>
                <p>For example, <code>projects.view</code> allows seeing projects, and <code>users.edit</code> allows editing user profiles.</p>

                <h3 class="border-b">Default roles</h3>
                <ul>
                    <li><strong>admin:</strong> All permissions, can assign any role</li>
                    <li><strong>user:</strong> Can create projects</li>
                </ul>
                <p>Administrators can create custom roles, copy existing ones, and configure which roles each role is allowed to assign to others.</p>
            </section>

            {{-- NAVIGATION --}}
            <section id="navigation">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Navigation manager</h2>
                <p>Administrators can customize the main navigation menu at <strong>Settings &rarr; Navigation</strong>.</p>
                <ul>
                    <li><strong>Add items</strong> with a label and URL</li>
                    <li><strong>Reorder</strong> using the up/down arrows</li>
                    <li><strong>Nest items</strong> by clicking the indent arrow to make an item a child of the one above it</li>
                    <li><strong>Outdent</strong> child items to promote them back to top level</li>
                    <li><strong>Set visibility</strong> by checking roles (admin, user) for each item</li>
                </ul>

                <h3 class="border-b">Guest access</h3>
                <p>Check the <strong>guest</strong> role on a nav item to make it visible to users who aren't logged in. This also makes the corresponding page publicly accessible. For example, checking "guest" on the Projects item lets anyone view the projects list and individual projects without logging in.</p>
                <p>If a child item has guest access, its parent dropdown automatically appears for guests too.</p>
            </section>

            {{-- SETTINGS --}}
            <section id="settings">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Settings</h2>
                <p>Application settings are managed at <strong>Settings</strong> (requires the settings.manage permission). Settings are organized by group:</p>

                <h3 class="border-b">Auth</h3>
                <ul>
                    <li><strong>Authentication mode:</strong> Email code, password, or password + 2FA</li>
                    <li><strong>Allow registration:</strong> Enable or disable new account creation</li>
                </ul>

                <h3 class="border-b">General</h3>
                <ul>
                    <li><strong>News:</strong> Rich text content displayed on the home page (a pulsing indicator on the site logo alerts users to updates they haven't seen)</li>
                    <li><strong>Publication year:</strong> Year for the copyright notice in the footer. Set to <code>0000</code> for the current year only, or enter a year like <code>2024</code> to show a range (e.g., "2024 - 2026")</li>
                </ul>

                <h3 class="border-b">Appearance</h3>
                <ul>
                    <li><strong>Accent color:</strong> Grayscale, green, blue, amber, or rose</li>
                    <li><strong>Font pairing:</strong> Choose from several display/body font combinations, or use the system default</li>
                </ul>

                <h3 class="border-b">Trash</h3>
                <ul>
                    <li><strong>Retention days:</strong> How long deleted items stay in trash before automatic cleanup (default 30 days)</li>
                    <li><strong>Auto cleanup:</strong> Enable or disable automatic removal of expired trash items</li>
                </ul>

                <p>Changes are saved via AJAX. Press <strong>Ctrl+S</strong> or click the save indicator to save.</p>
            </section>

            {{-- THEMES --}}
            <section id="themes">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Themes and appearance</h2>

                <h3 class="border-b">Dark and light mode</h3>
                <p>Click the sun/moon icon in the navigation bar to toggle between light and dark themes. Your preference is saved to your profile and remembered across sessions.</p>

                <h3 class="border-b">Accent colors</h3>
                <p>The accent color affects buttons, links, highlights, and interactive elements across the entire application. Choose from grayscale, green, blue, amber, or rose in settings.</p>

                <h3 class="border-b">Font pairings</h3>
                <p>Each font pairing sets a display font (used for headings, buttons, and labels) and a body font (used for paragraphs and general text). All fonts are hosted locally: No external requests are made.</p>
            </section>

            {{-- ACTIVITY LOG --}}
            <section id="activity-log">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Activity log</h2>
                <p>The activity log records all changes made to the application. Each entry shows:</p>
                <ul>
                    <li>What was changed (model type and name)</li>
                    <li>What action was taken (created, updated, deleted, restored)</li>
                    <li>Who made the change</li>
                    <li>When it happened</li>
                </ul>
                <p>Click <strong>View</strong> on a log entry to see the full details, including the exact field changes with old and new values.</p>
                <p>Use the filters to narrow the log by model type, action, user, date range, or search term.</p>
            </section>

            {{-- TRASH --}}
            <section id="trash">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Trash</h2>
                <p>When you delete an item, it moves to the trash instead of being permanently removed. From the trash, you can:</p>
                <ul>
                    <li><strong>Restore:</strong> Put the item back where it was, including its relationships (e.g., a user's role assignments)</li>
                    <li><strong>Delete permanently:</strong> Remove the item forever</li>
                    <li><strong>Empty trash:</strong> Permanently delete all items in the trash</li>
                </ul>
                <p>Items are automatically removed from trash after the configured retention period (default 30 days) if auto-cleanup is enabled.</p>
            </section>

            {{-- KEYBOARD SHORTCUTS --}}
            <section id="keyboard">
                <h2 class="bg-graymatter-panel-light rounded-sm p-2">Keyboard shortcuts</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left p-2 border-b border-divider">Shortcut</th>
                                <th class="text-left p-2 border-b border-divider">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+S</code></td><td class="p-2 border-b border-divider/50">Save (on pages with AJAX save)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+B</code></td><td class="p-2 border-b border-divider/50">Bold (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+I</code></td><td class="p-2 border-b border-divider/50">Italic (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+U</code></td><td class="p-2 border-b border-divider/50">Underline (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+Z</code></td><td class="p-2 border-b border-divider/50">Undo (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+Shift+Z</code></td><td class="p-2 border-b border-divider/50">Redo (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+Shift+E</code></td><td class="p-2 border-b border-divider/50">Center align (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+Shift+L</code></td><td class="p-2 border-b border-divider/50">Left align (in editor)</td></tr>
                            <tr><td class="p-2 border-b border-divider/50"><code>Ctrl+Shift+R</code></td><td class="p-2 border-b border-divider/50">Right align (in editor)</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-text-muted text-sm mt-2">On macOS, use <code>Cmd</code> instead of <code>Ctrl</code>.</p>
            </section>
        </div>
    </div>
</x-layouts.app>
