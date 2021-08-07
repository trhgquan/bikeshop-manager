@extends('template')

@section('title', 'Dashboard')

@section('page-title')
<h1 class="display-3">Dashboard</h3>
@endsection

@section('page-content')
<div class="dropdown">
  <button class="btn btn-success dropdown-toggle" id="addMenuDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" type="button">
    Thêm mới
  </button>
  <ul class="dropdown-menu" aria-labelledby="addMenuDropdownBtn">
    <li><a class="dropdown-item" href="{{ route('brands.create') }}">Hãng xe</a></li>
    <li><a class="dropdown-item" href="{{ route('bikes.create') }}">Loại xe</a></li>
    <li><a class="dropdown-item" href="{{ route('orders.create') }}">Đơn hàng</a></li>
  </ul>
</div>
@endsection