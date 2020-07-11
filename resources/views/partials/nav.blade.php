<div class="brand">
  <a href="{{ url('/')}} ">{{ config('app.name') }}</a>
</div>
<div class="nav-links-mobile">
  <i class="fas fa-bars"></i>
</div>
<div class="nav-links">
  <ul>
    <li><a href="{{ url('/')}} ">Item 1</a></li>
    <li><a href="{{ url('/')}} ">Item 2</a></li>
    <li><a href="{{ url('/')}} ">Item 3</a></li>
  </ul>
  <ul>
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
      <li class="dropdown">
        <a>{{ Auth::user()->name }} <i class="fas fa-caret-down"></i></a>
        <ul>
          <li>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
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
