@extends('content.brand.layouts')

@section('title', 'Hãng xe ' . $brand->brand_name)

@section('page-small-title')
<small class="lead">Thông tin hãng xe {{ $brand->brand_name }}</small>
@endsection

@section('page-table')
<div class="row">
  <div class="col-sm">
    <dl class="row">
      <dt class="col-sm-3">Tên hãng xe</dt>
      <dl class="col-sm-9">{{ $brand->brand_name }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Mô tả</dt>
      <dl class="col-sm-9">{{ $brand->brand_description }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Tạo bởi</dt>
      <dl class="col-sm-9">{{ $brand->created_by->nameAndUsername() }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Ngày tạo</dt>
      <dl class="col-sm-9">{{ $brand->created_at }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Sửa lần cuối</dt>
      <dl class="col-sm-9">{{ $brand->updated_by->nameAndUsername() }}</dl>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Ngày sửa</dt>
      <dl class="col-sm-9">{{ $brand->updated_at }}</dl>
    </dl>
  </div>
</div>
<a class="btn btn-warning" href="{{ route('brands.edit', $brand->id) }}">Chỉnh sửa hãng xe {{ $brand->brand_name }}</a>
<hr>

<small class="lead list-bikes">Danh sách các loại xe thuộc hãng {{ $brand->brand_name }}</small>
@if ($bikes->count() > 0)
@include('table.bike-list', ['brand' => $brand, 'bikes' => $bikes])
@else
Chưa có loại xe nào!
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