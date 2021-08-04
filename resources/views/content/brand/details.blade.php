@extends('content.brand.layouts')

@section('title', 'Hang xe ' . $brand->brand_name)

@section('page-table')
Hang xe: {{ $brand->brand_name }}<br/>
Mo ta:<br/>
{{ $brand->brand_description }}<br/>
Tao boi: {{ $brand->created_by->nameAndUsername() }}<br/>
Sua lan cuoi: {{ $brand->updated_by->nameAndUsername() }}<br/>
Ngay tao: {{ $brand->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $brand->updated_at->format('d-m-Y') }}<br/>
<a href="{{ route('brands.edit', $brand->id) }}">Chinh sua hang xe</a><br/>
Danh sach loai xe thuoc hang {{ $brand->brand_name }}:<br/>

@if ($bikes->count() > 0)
@include('table.bike-list', ['brand' => $brand, 'bikes' => $bikes])
@else
Chua co loai xe nao!
@endif
@endsection

@section('javascripts')
<script type="text/javascript">
$(document).ready(function() {
  $('#bikesTable').DataTable();
});
</script>
@endsection