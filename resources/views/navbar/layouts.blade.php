<div class="col-md-2 position-fixed d-md-block d-flex flex-column flex-shrink-0 p-3 bg-dark sidebar">
  <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <span class="fs-4">{{ config('app.name') }}</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="{{ route('dashboard') }}" class="nav-link text-white {{ Request::routeIs('dashboard') ? 'active' : '' }}">
        Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="btn-toggle nav-link text-white collapsed" data-bs-toggle="collapse" data-bs-target="#brands-collapse" aria-expanded="false">
        Quản lý hãng xe
      </a>
      <div id="brands-collapse" class="collapse">
        <ul class="btn-toggle-nav fw-normal pb-1 small">
          <li>
            <a href="{{ route('brands.create') }}" class="nav-link text-white {{ Request::routeIs('brands.create') ? 'active' : '' }}">
            Thêm hãng xe mới
            </a>
          </li>
          <li>
            <a href="{{ route('brands.index') }}" class="nav-link text-white {{ Request::routeIs('brands.index') ? 'active' : '' }}">
            Danh sách
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a href="#" class="btn-toggle nav-link text-white collapsed" data-bs-toggle="collapse" data-bs-target="#bikes-collapse" aria-expanded="false">
        Quản lý loại xe
      </a>
      <div id="bikes-collapse" class="collapse">
        <ul class="btn-toggle-nav fw-normal pb-1 small">
          <li>
            <a href="{{ route('bikes.create') }}" class="nav-link text-white {{ Request::routeIs('bikes.create') ? 'active' : '' }}">
            Thêm loại xe mới
            </a>
          </li>
          <li>
            <a href="{{ route('bikes.index') }}" class="nav-link text-white {{ Request::routeIs('bikes.index') ? 'active' : '' }}">
            Danh sách
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a href="{{ route('orders.index') }}" class="nav-link text-white">
        Quản lý đơn hàng
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="btn-toggle nav-link text-white collapsed" data-bs-toggle="collapse" data-bs-target="#report-collapse" aria-expanded="false">
        Báo cáo
      </a>
      <div id="report-collapse" class="collapse">
        <ul class="btn-toggle-nav fw-normal pb-1 small">
          <li>
            <a href="{{ route('report.out_of_stock') }}" class="nav-link text-white">
            Loại xe sắp hết
            </a>
          </li>
          <li>
            <a href="{{ route('report.month_quantity_stat.index') }}" class="nav-link text-white">
            Loại xe bán chạy trong tháng
            </a>
          </li>
          <li>
            <a href="{{ route('report.month_revenue_stat.index') }}" class="nav-link text-white">
            Doanh số theo tháng
            </a>
          </li>
        </ul>
      </div>
    </li>
  </ul>
  <hr>
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      <strong>{{ Auth::user()->nameAndUsername() }}</strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="userDropdown">
      <li>
        <a class="dropdown-item" href="{{ route('auth.changepassword.index') }}">
          Đổi mật khẩu
        </a>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form id="logoutForm" action="{{ route('auth.logout') }}" method="POST">
          @csrf
          <a type="submit" href="#" id="logoutBtn" class="dropdown-item">Đăng xuất</a>
        </form>
      </li>
    </ul>
  </div>
</div>
