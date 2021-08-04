@extends('content.orders.layouts')

@section('title', 'Quan ly don hang')

@section('page-table')
Danh sach cac don hang tren he thong:
@if ($orders->count() > 0)
@include('table.order-list', compact('orders'))
@else
Hien tai khong co don hang nao!
@endif
@endsection

@section('javascripts')
<script type="text/javascript">
$(document).ready(function() {
  $('#ordersTable').DataTable({
    'order': []
  });
});
</script>
@endsection