<ul>
  <li>
    <a href="{{ route('dashboard') }}">
      Dashboard
    </a>
  </li>
  <li>
    <a href="{{ route('auth.changepassword.view') }}">
      Doi mat khau
    </a>
  </li>
  <li>
    <form id="logoutForm" action="{{ route('auth.logout') }}" method="POST">
      <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
      <a type="submit" href="#" id="logoutBtn">Dang xuat</a>
    </form>
  </li>
</ul>