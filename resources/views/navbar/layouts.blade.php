<ul>
  <li>
    <a href="{{ route('dashboard') }}">
      Dashboard
    </a>
  </li>
  <li>
    <a href="{{ route('auth.changepassword.index') }}">
      Đổi mật khẩu
    </a>
  </li>
  <li>
    <form id="logoutForm" action="{{ route('auth.logout') }}" method="POST">
      @csrf
      <a type="submit" href="#" id="logoutBtn">Đăng xuất</a>
    </form>
  </li>
</ul>
<ul>
  <li>
    <a href="{{ route('brands.index') }}">
      Quản lý hãng xe
    </a>
  </li>
  <li>
    <a href="{{ route('bikes.index') }}">
      Quản lý loại xe
    </a>
  </li>
  <li>
    <a href="{{ route('orders.index') }}">
      Quản lý đơn hàng
    </a>
  </li>
  <li>Báo cáo</li>
  <ol>
    <a href="{{ route('report.out_of_stock') }}">
      Các loại xe sắp hết
    </a>
  </ol>
  <ol>
    <a href="{{ route('report.month_quantity_stat.index') }}">
      Các loại xe bán chạy
    </a>
  </ol>
  <ol>
    <a href="{{ route('report.month_revenue_stat.index') }}">
      Doanh số bán hàng
    </a>
  </ol>
</ul>