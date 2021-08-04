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
<ul>
  <li>
    <a href="{{ route('brands.index') }}">
      Quan ly hang xe
    </a>
  </li>
  <li>
    <a href="{{ route('bikes.index') }}">
      Quan ly loai xe
    </a>
  </li>
  <li>
    <a href="{{ route('orders.index') }}">
      Quan ly don hang
    </a>
  </li>
  <li>Bao cao</li>
  <ol>
    <a href="{{ route('report.out_of_stock') }}">
      Cac loai xe sap het
    </a>
  </ol>
  <ol>
    <a href="{{ route('report.month_quantity_stat.index') }}">
      Cac loai xe ban chay
    </a>
  </ol>
  <ol>Doanh so ban hang</ol>
</ul>