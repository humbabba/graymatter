
  <div class="brand text-xl flex flex-row flex-nowrap items-center">
    <a href="{{ url('/')}}">{{ config('app.name') }}</a>
  </div>
  <div class="nav-links-container pl-[20px] w-full">
    <div class="nav-links flex flex-row flex-nowrap justify-between items-center h-full text-lg">
      <ul class="nav-links-primary flex flex-row flex-nowrap items-center justify-start gap-4">
        @guest
        @else
          <li><a href="{{ url('/starter')}} ">Laravel starter app</a></li>
          <li><a href="{{ url('/loggers')}} ">Loggers</a></li>
        @endguest
        @role('admin')
          <li class="nav-links-dropdown cursor-pointer">
            <a>Admin <i class="fas fa-caret-down"></i></a>
            <ul class="nav-links-dropdown-submenu hidden">
              <li>
                <a href="/users">Users</a>
              </li>
              <li>
                <a href="/settings">Settings</a>
              </li>
            </ul>
          </li>
        @endrole
      </ul>
      <ul class="nav-links-secondary flex flex-row flex-nowrap gap-4">
        @guest
          <li>
              <a href="{{ route('login') }}">Log in</a>
          </li>
          @if (Route::has('register'))
            <li>
                <a href="{{ route('register') }}">Register</a>
            </li>
          @endif
        @else
          <li class="nav-links-dropdown flex flex-row flex-nowrap gap-4">
            <a><img src="{{ getGravatarSrc(Auth::user()->email,30) }}" class="gravatar-icon"/></a><a>{{ Auth::user()->name }} <i class="fas fa-caret-down"></i></a>
            <ul class="nav-links-dropdown-submenu hidden">
              <li>
                <a href="{{ route('dashboard')}}">Dashboard</a>
              </li>
              <li>
                <a href="{{ route('users.profile',Auth::user()->id)}}">Profile</a>
              </li>
              <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Log out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              </li>
            </ul>
          </li>
        @endguest
      </ul>
    </div>
  </div>
  <div class="nav-hamburger md-lg:hidden">
    <i class="fas fa-bars"></i>
  </div>
