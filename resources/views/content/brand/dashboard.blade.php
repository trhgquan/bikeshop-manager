@extends('content.brand.layouts')

@section('title', 'Quan ly hang xe')

@section('page-table')
Danh sach cac hang xe hien co:
@if ($brands->count() > 0)
@include('table.brand-list', compact('brands'))
@else
Hien tai khong co hang xe nao!
@endif
@endsection

@section('javascripts')
<script type="text/javascript">
$(document).ready(function() {
  $('#brandsTable').DataTable();
});
</script>
@endsection