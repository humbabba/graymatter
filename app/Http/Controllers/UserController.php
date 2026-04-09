<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public const ALLOWED_STARTING_VIEWS = [
        '/users' => ['label' => 'Users', 'permission' => 'users.view'],
        '/roles' => ['label' => 'Roles', 'permission' => 'roles.view'],
        '/activity-logs' => ['label' => 'Activity log', 'permission' => 'activity-logs.view'],
        '/trash' => ['label' => 'Trash', 'permission' => 'trash.view'],
        '/settings' => ['label' => 'Settings', 'permission' => 'settings.manage'],
    ];

    public static function getStartingViewsForUser($user): array
    {
        $views = ['' => 'Home (default)', '/projects' => 'Projects'];
        foreach (self::ALLOWED_STARTING_VIEWS as $path => $config) {
            if ($user->hasPermission($config['permission'])) {
                $views[$path] = $config['label'];
            }
        }
        return $views;
    }

    public function index(Request $request)
    {
        $allowedSorts = ['name', 'email', 'created_at', 'last_login_at'];
        $sort = in_array($request->input('sort'), $allowedSorts) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $query = User::with('roles')->orderBy($sort, $direction);

        if ($request->filled('search')) {
            $query->search($request->search, ['name', 'email']);
        }

        if ($request->filled('from') || $request->filled('to')) {
            $query->createdBetween($request->from, $request->to);
        }

        $users = $query->paginate(20)->withQueryString();
        $assignableRoleIds = auth()->check() ? $this->getAssignableRoles()->pluck('id')->toArray() : [];

        if (auth()->check()) {
            auth()->user()->update(['users_viewed_at' => now()]);
        }

        return view('users.index', compact('users', 'assignableRoleIds', 'sort', 'direction'));
    }

    public function create()
    {
        $roles = $this->getAssignableRoles();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $assignableRoleIds = $this->getAssignableRoles()->pluck('id')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', 'exists:roles,id', function ($attribute, $value, $fail) use ($assignableRoleIds) {
                if (!in_array((int) $value, $assignableRoleIds)) {
                    $fail('You are not allowed to assign this role.');
                }
            }],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->roles()->attach($validated['role']);

        return redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function show(User $user)
    {
        if (auth()->id() !== $user->id && !auth()->user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $user->load('roles');
        $assignableRoleIds = $this->getAssignableRoles()->pluck('id')->toArray();

        return view('users.show', compact('user', 'assignableRoleIds'));
    }

    public function edit(User $user)
    {
        $isOwnProfile = auth()->id() === $user->id;

        if (!$isOwnProfile && !auth()->user()->hasPermission('users.edit')) {
            return redirect()->route('users.show', $user);
        }

        $roles = $this->getAssignableRoles();

        if (!$isOwnProfile && array_diff($user->roles->pluck('id')->toArray(), $roles->pluck('id')->toArray())) {
            return redirect()->route('users.show', $user);
        }

        $userRoleIds = $user->roles->pluck('id')->toArray();
        $startingViews = self::getStartingViewsForUser($user);
        $authMode = \App\Models\AppSetting::get('auth_mode', 'code');

        return view('users.edit', compact('user', 'roles', 'userRoleIds', 'isOwnProfile', 'startingViews', 'authMode'));
    }

    public function update(Request $request, User $user)
    {
        $isOwnProfile = auth()->id() === $user->id;

        if (!$isOwnProfile && !auth()->user()->hasPermission('users.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $assignableRoles = $this->getAssignableRoles();

        if (!$isOwnProfile) {
            $this->authorizeUserRoles($user, $assignableRoles);
        }

        $assignableRoleIds = $assignableRoles->pluck('id')->toArray();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'starting_view' => ['nullable', 'string', 'in:' . implode(',', array_keys(self::getStartingViewsForUser($user)))],
            'theme' => ['nullable', 'in:light,dark'],
        ];

        // Only allow role changes if user has users.edit permission
        if (auth()->user()->hasPermission('users.edit')) {
            $rules['role'] = ['required', 'exists:roles,id', function ($attribute, $value, $fail) use ($assignableRoleIds) {
                if (!in_array((int) $value, $assignableRoleIds)) {
                    $fail('You are not allowed to assign this role.');
                }
            }];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'starting_view' => $validated['starting_view'] ?? null,
            'theme' => $validated['theme'] ?? null,
        ];

        if ($isOwnProfile && $user->isAppAdmin()) {
            $updateData['notify_on_new_user'] = $request->boolean('notify_on_new_user');
            $updateData['notify_on_create'] = $request->input('notify_on_create', []);
        }

        $user->update($updateData);

        if (auth()->user()->hasPermission('users.edit') && isset($validated['role'])) {
            $user->roles()->sync([$validated['role']]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully.']);
        }

        return redirect()->route('users.show', $user)->with('status', 'User updated successfully.');
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        $request->user()->update(['theme' => $validated['theme']]);

        return response()->json(['success' => true]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('status', 'User deleted successfully.');
    }

    private function authorizeUserRoles(User $user, $assignableRoles): void
    {
        $assignableRoleIds = $assignableRoles->pluck('id')->toArray();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        if (array_diff($userRoleIds, $assignableRoleIds)) {
            abort(403, 'You do not have permission to manage users with this role.');
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        if (auth()->id() !== $user->id) {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!$user->hasPassword() || !\Hash::check($request->current_password, $user->password)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = \Hash::make($request->password);
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Password updated.']);
        }

        return back()->with('status', 'Password updated.');
    }

    private function getAssignableRoles()
    {
        return auth()->user()->roles()
            ->with('assignableRoles')
            ->get()
            ->pluck('assignableRoles')
            ->flatten()
            ->unique('id')
            ->values();
    }
}
