  <div class="brand text-2xl flex flex-row flex-nowrap items-center">
    <a href="{{ url('/')}}">{{ config('app.name') }}</a>
  </div>
    <div class="nav-links hidden md-lg:flex flex-row flex-nowrap justify-between items-center h-full text-lg w-full">
      <ul class="nav-links-primary flex flex-row flex-nowrap items-center px-[20px] h-full">
        @guest
        @else
          <li>
            <a href="{{ url('/starter')}} ">Laravel starter app</a>
          </li>
          <li><a href="{{ url('/loggers')}} ">Loggers</a></li>
        @endguest
        @role('admin')
          <li class="nav-links-dropdown cursor-pointer">
            <a class="flex flex-row flex-nowrap gap-[8px] items-center">
              Admin {!!Helpers::getSvgCodeWithClasses('chevron-down.svg',['h-[20px]']) !!}
            </a>
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
      <ul class="nav-links-secondary flex flex-row flex-nowrap items-center gap-[12px]">
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
          <li class="nav-links-dropdown" @click="toggle = !toggle">
            <a class="flex flex-row flex-nowrap gap-[8px] items-center">
                <img src="{{ Helpers::getGravatarSrc(Auth::user()->email,30) }}" class="gravatar-icon"/>
                {{ Auth::user()->name }}
                {!! Helpers::getSvgCodeWithClasses('chevron-down.svg',['h-[20px]']) !!}
            </a>
            <ul class="nav-links-dropdown-submenu" v-show="toggle">
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
  <div class="nav-hamburger md-lg:hidden flex flex-row items-center">
    {!!Helpers::getSvgCodeWithClasses('bars-3.svg',['w-[22px]']) !!}
  </div>
