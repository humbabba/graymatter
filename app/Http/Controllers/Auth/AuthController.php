<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AuthCodeNotification;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected function authMode(): string
    {
        return AppSetting::get('auth_mode', 'code');
    }

    // ── Login ───────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login', ['authMode' => $this->authMode()]);
    }

    public function login(Request $request)
    {
        $mode = $this->authMode();

        if ($mode === 'code') {
            return $this->handleCodeLogin($request);
        }

        return $this->handlePasswordLogin($request, $mode);
    }

    protected function handleCodeLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        return $this->sendCodeAndRedirect($user);
    }

    protected function handlePasswordLogin(Request $request, string $mode)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        // User exists but has no password — redirect to set-password flow
        if (!$user->hasPassword()) {
            return $this->sendCodeAndRedirect($user, 'set-password');
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        // Password valid — for password-only mode, login is complete
        if ($mode === 'password') {
            $this->completeLogin(Auth::user());
            $request->session()->regenerate();
            return redirect(Auth::user()->starting_view ?: '/');
        }

        // Password + 2FA mode — need a code
        Auth::logout();
        return $this->sendCodeAndRedirect($user);
    }

    protected function sendCodeAndRedirect(User $user, string $flow = 'verify'): \Illuminate\Http\RedirectResponse
    {
        $code = $user->generateAuthCode();

        if (app()->environment('local')) {
            session()->flash('dev_code', $code);
        } else {
            $user->notify(new AuthCodeNotification($code));
        }

        session(['auth_user_id' => $user->id, 'auth_flow' => $flow]);

        if ($flow === 'set-password') {
            return redirect()->route('login.set-password')
                ->with('status', 'You need to set a password. A verification code has been sent to your email.');
        }

        return redirect()->route('login.verify')
            ->with('status', 'A verification code has been sent to your email.');
    }

    // ── Verify code ─────────────────────────────────────────

    public function showVerify()
    {
        if (!session('auth_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = User::find(session('auth_user_id'));

        if (!$user || !$user->verifyAuthCode($request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user->clearAuthCode();
        $this->completeLogin($user);

        Auth::login($user, remember: true);

        $request->session()->forget(['auth_user_id', 'auth_flow']);
        $request->session()->regenerate();

        return redirect($user->starting_view ?: '/');
    }

    // ── Set password (existing users without one) ───────────

    public function showSetPassword()
    {
        if (!session('auth_user_id') || session('auth_flow') !== 'set-password') {
            return redirect()->route('login');
        }

        return view('auth.set-password');
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find(session('auth_user_id'));

        if (!$user || !$user->verifyAuthCode($request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user->clearAuthCode();
        $user->password = Hash::make($request->password);
        $user->save();
        $this->completeLogin($user);

        Auth::login($user, remember: true);

        $request->session()->forget(['auth_user_id', 'auth_flow']);
        $request->session()->regenerate();

        return redirect($user->starting_view ?: '/');
    }

    protected function completeLogin(User $user): void
    {
        $isFirstLogin = is_null($user->last_login_at);
        $user->update(['last_login_at' => now()]);

        if ($isFirstLogin) {
            $admins = User::where('notify_on_new_user', true)
                ->where('id', '!=', $user->id)
                ->whereHas('roles', fn($q) => $q->where('name', 'admin'))
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewUserRegisteredNotification($user));
            }
        }
    }

    // ── Logout ──────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ── Registration ────────────────────────────────────────

    public function showRegister()
    {
        if (!AppSetting::get('allow_registration', true)) {
            abort(404);
        }

        return view('auth.register', ['authMode' => $this->authMode()]);
    }

    public function register(Request $request)
    {
        if (!AppSetting::get('allow_registration', true)) {
            abort(404);
        }

        $mode = $this->authMode();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ];

        if (in_array($mode, ['password', 'password_2fa'])) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if (in_array($mode, ['password', 'password_2fa'])) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole);
        }

        // In password modes, auto-login after registration
        if (in_array($mode, ['password', 'password_2fa'])) {
            $this->completeLogin($user);
            Auth::login($user, remember: true);
            return redirect($user->starting_view ?: '/');
        }

        // In code mode, send a verification code
        return $this->sendCodeAndRedirect($user);
    }
}
