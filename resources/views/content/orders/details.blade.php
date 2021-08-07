@extends('content.orders.layouts')

@section('title', 'Chi tiết đơn hàng')

@section('page-small-title')
<small class="lead">Chi tiết đơn hàng</small>
@endsection

@section('page-table')
<div class="row">
  <div class="col-sm">
    <dl class="row">
      <dt class="col-sm-3">Tên khách hàng</dt>
      <dl class="col-sm-9">{{ $order->customer_name }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Email khách hàng</dt>
      <dl class="col-sm-9">{{ $order->customer_email }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Tạo bởi</dt>
      <dd class="col-sm-9">{{ $order->created_by->nameAndUsername() }}</dd>
    </dl> 
    <dl class="row">
      <dt class="col-sm-3">Ngày tạo</dt>
      <dd class="col-sm-9">{{ $order->created_at }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Sửa lần cuối</dt>
      <dd class="col-sm-9">{{ $order->updated_by->nameAndUsername() }}</dd>
    </dl> 
    <dl class="row">
      <dt class="col-sm-3">Ngày sửa</dt>
      <dd class="col-sm-9">{{ $order->updated_at }}</dd>
    </dl>
    <a class="btn btn-warning" href="{{ route('orders.edit', $order->id) }}">Chỉnh sửa đơn hàng</a>
  </div>
  <div class="col-sm">
    @include('table.invoice-list', compact('detail', 'order'))
  </div>
</div>
@endsection