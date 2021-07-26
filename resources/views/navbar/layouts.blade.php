<ul>
  <li>
    <a href="{{ route('dashboard') }}">
      Dashboard
    </a>
  </li>
  <li>
    <a href="{{ route('auth.changepassword.index') }}">
      Doi mat khau
    </a>
  </li>
  <li>
    <form id="logoutForm" action="{{ route('auth.logout') }}" method="POST">
      @csrf
      <a type="submit" href="#" id="logoutBtn">Dang xuat</a>
    </form>
  </li>
</ul>