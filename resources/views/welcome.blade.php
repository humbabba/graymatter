@php use App\Models\AppSetting; @endphp
<x-layouts.app>
    <div class="flex items-center justify-center w-full h-full">
        <div class="text-center">
            <div class="mb-8 text-left">
                {!! AppSetting::get('news') ?? '<h1 class="text-4xl mb-4">Welcome to ' . e(config('app.name', 'Graymatter')) . '</h1><p>All is well.</p>' !!}
            </div>
            @guest
                @if(AppSetting::get('allow_registration', true))
                    <p class="mt-6 text-sm">Want to create a project? <a href="{{ route('register') }}" target="_blank">Register for free</a>.</p>
                @endif
            @endguest
        </div>
    </div>
</x-layouts.app>
