<x-layouts.app>
    <div class="flex items-center justify-center h-full">
        <div class="w-full max-w-md">
            <div class="bg-graymatter-panel-light rounded-sm overflow-hidden">
                <div class="h-2 bg-graymatter-green"></div>

                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-graymatter-green">Verify code</h1>
                        <p class="text-text-muted text-sm mt-2">Enter the 6-digit code sent to your email.</p>
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

                    <form method="POST" action="{{ route('login.verify.submit') }}">
                        @csrf
                        <div class="mb-6">
                            <label for="code" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Verification code</label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                required
                                autofocus
                                maxlength="6"
                                pattern="[0-9]{6}"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                class="w-full p-4 bg-graymatter-panel border border-divider rounded-sm text-text text-center text-2xl font-mono tracking-[0.5em] focus:border-graymatter-teal focus:ring-2 focus:ring-graymatter-teal/20"
                            >
                        </div>

                        <button type="submit" class="w-full py-4 rounded-none bg-graymatter-green text-graymatter-black font-bold uppercase tracking-wider transition-all hover:shadow-lg hover:shadow-graymatter-green/30" style="font-family: var(--font-display);">
                            Verify
                        </button>
                    </form>

                    <p class="mt-6 text-center text-text-muted text-sm">
                        <a href="{{ route('login') }}" class="text-graymatter-teal hover:text-graymatter-lime">Back to login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
