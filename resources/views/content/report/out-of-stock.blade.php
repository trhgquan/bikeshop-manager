@extends('content.report.layouts')

@section('title', 'Các loại xe sắp hết hàng - Báo cáo')

@section('page-small-title')
<small class="lead">
  Các loại xe sắp hết hàng
</small>
@endsection

@section('page-table')
@if ($items->count() > 0)
Danh sách các loại xe sắp hết hàng:
@include('table.out-of-stock-list', ['items' => $items])
@else
Không có loại xe nào sắp hết hàng!
@endif
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="{{ asset('js/vn-datatable.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#outOfStockTable').DataTable(settings);
});
</script>
@endsection