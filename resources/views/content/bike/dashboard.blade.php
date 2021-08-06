@extends('content.bike.layouts')

@section('title', 'Quản lý loại xe')

@section('page-small-title')
<small class="lead">Danh sách các loại xe hiện có:</small>
@endsection

@section('page-table')
@if ($bikes->count() > 0)
@include('table.bike-list', ['bikes' => $bikes])
@else
Hiện tại không có loại xe nào!
@endif
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="{{ asset('js/vn-datatable.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#bikesTable').DataTable(language);
});
</script>
@endsection