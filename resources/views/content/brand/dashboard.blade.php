@extends('content.brand.layouts')

@section('title', 'Quản lý hãng xe')

@section('page-small-title')
<small class="lead">Danh sách các hãng xe hiện có</small>
@endsection

@section('page-table')
@if ($brands->count() > 0)
@include('table.brand-list', compact('brands'))
@else
Hiện tại không có hãng xe nào!
@endif
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="{{ asset('js/vn-datatable.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#brandsTable').DataTable(settings);
});
</script>
@endsection