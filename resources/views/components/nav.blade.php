<script>window.__auth = @json(auth()->check());</script>
@php
    $navItems = \App\Models\NavItem::tree();
    $hasNewUsers = false;
    if (auth()->check()) {
        $viewedAt = auth()->user()->users_viewed_at;
        $query = \App\Models\User::where('id', '!=', auth()->id());
        if ($viewedAt) {
            $query->where('created_at', '>', $viewedAt);
        }
        $hasNewUsers = $query->exists();
    }
@endphp
<nav class="bg-graymatter-dark border-b border-divider" x-data="{ mobileMenuOpen: false }">
    <div class="flex items-center h-16 px-4 md:px-6">
        <!-- Logo / Brand -->
        <a href="/?home" class="flex items-center gap-3 text-graymatter-green hover:text-graymatter-lime no-underline relative shrink-0">
            <span class="text-xl md:text-2xl font-semibold tracking-wider" style="font-family: var(--font-display);">
                {{ config('app.name', 'WestStar') }}
            </span>
            @auth
                @php
                    $newsUpdatedAt = \App\Models\AppSetting::where('key', 'news')->value('updated_at');
                    $newsViewedAt = auth()->user()->news_viewed_at;
                    $hasUnreadNews = $newsUpdatedAt && (!$newsViewedAt || \Carbon\Carbon::parse($newsUpdatedAt)->gt($newsViewedAt));
                @endphp
                @if($hasUnreadNews)
                    <span class="absolute -top-1 -right-3 w-3 h-3 rounded-none bg-graymatter-lime animate-pulse"></span>
                @endif
            @endauth
        </a>

        <!-- Desktop Navigation Links -->
        <div class="hidden md:flex items-center gap-1 ml-10" style="font-family: var(--font-display);">
            @foreach($navItems as $item)
                @if($item->isVisibleTo(auth()->user()))
                    @php
                        $visibleChildren = $item->children->filter(fn($c) => $c->isVisibleTo(auth()->user()));
                        $itemHasNewUsers = $hasNewUsers && ($item->url === '/users' || $visibleChildren->contains(fn($c) => $c->url === '/users'));
                    @endphp
                    @if($visibleChildren->count())
                        {{-- Dropdown --}}
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-none bg-graymatter-panel text-graymatter-lime hover:bg-graymatter-lime hover:text-white transition-all duration-200 uppercase text-sm tracking-wider">
                                {{ $item->label }}
                                @if($itemHasNewUsers)
                                    <span class="w-3 h-3 rounded-none bg-graymatter-lime animate-pulse"></span>
                                @endif
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1 hidden group-hover:block min-w-[180px] z-50">
                                <div class="bg-graymatter-panel border border-divider rounded-sm overflow-hidden">
                                    @foreach($visibleChildren as $child)
                                        <a href="{{ $child->url }}" class="block px-4 py-3 text-text hover:bg-graymatter-lime hover:text-white transition-colors no-underline text-sm">
                                            {{ $child->label }}
                                            @if($hasNewUsers && $child->url === '/users')
                                                <span class="w-3 h-3 rounded-none bg-graymatter-lime animate-pulse" style="display:inline-block;margin-left:0.5rem"></span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Plain link --}}
                        <a href="{{ $item->url }}"
                           class="relative flex items-center gap-2 px-4 py-2 rounded-none bg-graymatter-panel text-graymatter-green hover:bg-graymatter-green hover:text-white transition-all duration-200 no-underline uppercase text-sm tracking-wider">
                            {{ $item->label }}
                            @if($itemHasNewUsers)
                                <span class="w-3 h-3 rounded-none bg-graymatter-lime animate-pulse"></span>
                            @endif
                        </a>
                    @endif
                @endif
            @endforeach
        </div>

        <!-- Desktop User Section -->
        <div class="hidden md:flex ml-auto items-center gap-4">
            <!-- Theme Toggle -->
            <button
                x-data="{ dark: document.documentElement.getAttribute('data-theme') === 'dark' }"
                @click="dark = !dark; document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light'); localStorage.setItem('theme', dark ? 'dark' : 'light'); if (window.__auth) fetch('/user/theme', { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ theme: dark ? 'dark' : 'light' }) })"
                class="p-2 rounded-none text-text-muted hover:text-text transition-colors"
                title="Toggle theme"
            >
                <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            @auth
                <a href="{{ route('users.show', Auth::user()) }}" class="flex items-center gap-3 no-underline hover:opacity-80 transition-opacity">
                    <x-avatar :user="Auth::user()" :size="8" />
                    <span class="text-text-muted text-sm">{{ Auth::user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-none bg-graymatter-panel text-graymatter-lime hover:bg-graymatter-lime hover:text-white transition-all duration-200 uppercase text-xs tracking-wider" style="font-family: var(--font-display);">
                        Log out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-none bg-graymatter-green text-white hover:shadow-lg hover:shadow-graymatter-green/30 transition-all duration-200 no-underline uppercase text-sm tracking-wider" style="font-family: var(--font-display);">
                    Log in
                </a>
                @if(\App\Models\AppSetting::get('allow_registration', true))
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-none bg-graymatter-panel hover:shadow-lg transition-all duration-200 no-underline uppercase text-sm tracking-wider" style="font-family: var(--font-display);">
                        Register
                    </a>
                @endif
            @endauth
        </div>

        <!-- Mobile Theme Toggle + Hamburger -->
        <div class="md:hidden ml-auto flex items-center gap-1">
            <button
                x-data="{ dark: document.documentElement.getAttribute('data-theme') === 'dark' }"
                @click="dark = !dark; document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light'); localStorage.setItem('theme', dark ? 'dark' : 'light'); if (window.__auth) fetch('/user/theme', { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ theme: dark ? 'dark' : 'light' }) })"
                class="p-2 text-text-muted hover:text-text transition-colors"
                title="Toggle theme"
            >
                <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-graymatter-green hover:text-graymatter-lime transition-colors" style="font-family: var(--font-display);">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="mobileMenuOpen = false"
         class="md:hidden border-t border-divider bg-graymatter-dark px-4 pb-4"
         style="font-family: var(--font-display);">

        @foreach($navItems as $item)
            @if($item->isVisibleTo(auth()->user()))
                <div class="py-2 border-b border-divider/50">
                    @php
                        $visibleChildren = $item->children->filter(fn($c) => $c->isVisibleTo(auth()->user()));
                        $itemHasNewUsers = $hasNewUsers && ($item->url === '/users' || $visibleChildren->contains(fn($c) => $c->url === '/users'));
                    @endphp
                    @if($visibleChildren->count())
                        <div class="text-graymatter-lime uppercase text-xs tracking-wider mb-2 px-2">
                            {{ $item->label }}
                        </div>
                        @foreach($visibleChildren as $child)
                            <a href="{{ $child->url }}" @click="mobileMenuOpen = false" class="block px-3 py-2 text-text hover:bg-graymatter-lime hover:text-white transition-colors no-underline text-sm rounded-sm">
                                {{ $child->label }}
                                @if($hasNewUsers && $child->url === '/users')
                                    <span class="w-3 h-3 rounded-none bg-graymatter-lime animate-pulse" style="display:inline-block;margin-left:0.5rem"></span>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <a href="{{ $item->url }}" @click="mobileMenuOpen = false" class="relative block px-3 py-2 text-text hover:bg-graymatter-green hover:text-white transition-colors no-underline text-sm rounded-sm">
                            {{ $item->label }}
                            @if($itemHasNewUsers)
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 w-3 h-3 rounded-none bg-graymatter-lime animate-pulse"></span>
                            @endif
                        </a>
                    @endif
                </div>
            @endif
        @endforeach

        @auth
            {{-- User Section --}}
            <div class="pt-3 flex items-center justify-between">
                <a href="{{ route('users.show', Auth::user()) }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 no-underline hover:opacity-80 transition-opacity">
                    <x-avatar :user="Auth::user()" :size="8" />
                    <span class="text-text-muted text-sm">{{ Auth::user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-none bg-graymatter-panel text-graymatter-lime hover:bg-graymatter-lime hover:text-white transition-all duration-200 uppercase text-xs tracking-wider">
                        Log Out
                    </button>
                </form>
            </div>
        @else
            <div class="py-3">
                <a href="{{ route('login') }}" class="block text-center px-4 py-2 rounded-none bg-graymatter-green text-white hover:shadow-lg hover:shadow-graymatter-green/30 transition-all duration-200 no-underline uppercase text-sm tracking-wider">
                    Log In
                </a>
            </div>
        @endauth
    </div>
</nav>
