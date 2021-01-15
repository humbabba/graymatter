
  <div class="brand">
    <a href="{{ url('/')}} ">{{ config('app.name') }}</a>
  </div>
  <div class="nav-links-container">
    <div class="nav-links">
      <ul class="nav-links-primary">
        @guest
        @else
          <li><a href="{{ url('/news')}} ">News</a></li>
          <li><a href="{{ url('/sports')}} ">Sports</a></li>
          <li class="nav-links-dropdown">
            <a>Weather <i class="fas fa-caret-down"></i></a>
            <ul class="nav-links-dropdown-submenu">
              <li>
                <a href="/forecast">Forecast</a>
              </li>
              <li>
                <a href="/radar">Radar</a>
              </li>
            </ul>
          </li>
        @endguest
        @role('admin')
          <li class="nav-links-dropdown">
            <a>Admin <i class="fas fa-caret-down"></i></a>
            <ul class="nav-links-dropdown-submenu">
              <li>
                <a href="/users">Users</a>
              </li>
              <li>
                <a href="/radar">Settings</a>
              </li>
            </ul>
          </li>
        @endrole
      </ul>
      <ul class="nav-links-secondary">
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
          <li class="nav-links-dropdown">
            <a>{{ Auth::user()->name }} <i class="fas fa-caret-down"></i></a>
            <ul class="nav-links-dropdown-submenu">
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
  <div class="nav-hamburger">
    <i class="fas fa-bars"></i>
  </div>
