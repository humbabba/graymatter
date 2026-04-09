<x-layouts.app>
    <div class="flex items-center justify-center h-full">
        <div class="w-full max-w-md">
            <div class="bg-graymatter-panel-light rounded-sm overflow-hidden">
                <div class="h-2 bg-graymatter-green"></div>

                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-graymatter-green">Login</h1>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 p-4 bg-graymatter-green/20 border border-graymatter-green/50 text-graymatter-green rounded-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('dev_code'))
                        <div class="mb-6 p-4 bg-graymatter-teal/20 border border-graymatter-teal/50 rounded-sm">
                            <p class="text-graymatter-teal font-semibold mb-2" style="font-family: var(--font-display);">Local dev code</p>
                            <p class="text-graymatter-lime text-2xl font-mono tracking-widest">{{ session('dev_code') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="mb-6">
                            <label for="email" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Email address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full p-4 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal focus:ring-2 focus:ring-graymatter-teal/20"
                            >
                        </div>

                        @if ($authMode !== 'code')
                            <div class="mb-6">
                                <label for="password" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    class="w-full p-4 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal focus:ring-2 focus:ring-graymatter-teal/20"
                                >
                            </div>
                        @endif

                        <button type="submit" class="w-full py-4 rounded-none bg-graymatter-green text-graymatter-black font-bold uppercase tracking-wider transition-all hover:shadow-lg hover:shadow-graymatter-green/30" style="font-family: var(--font-display);">
                            {{ $authMode === 'code' ? 'Send login code' : 'Log in' }}
                        </button>
                    </form>

                    @if(\App\Models\AppSetting::get('allow_registration', true))
                        <p class="mt-6 text-center text-text-muted text-sm">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-graymatter-teal hover:text-graymatter-lime">Register</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
