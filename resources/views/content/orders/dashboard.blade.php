@extends('content.orders.layouts')

@section('title', 'Quản lý đơn hàng')

@section('page-small-title')
<small class="lead">Danh sách các đơn hàng trên hệ thống</small>
@endsection

@section('page-table')
@if ($orders->count() > 0)
@include('table.order-list', compact('orders'))
@else
Hiện tại không có đơn hàng nào!
@endif
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="{{ asset('js/vn-datatable.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  // No order since results were ordered by created_at at query.
  settings.order = [];
  $('#ordersTable').DataTable(settings);
});
</script>
@endsection