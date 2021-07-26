@extends('content.brand.general')

@section('page-table')
@if (isset($brand))
Hang xe: {{ $brand->brand_name }}<br/>
Mo ta:<br/>
{{ $brand->brand_description }}<br/>
Ngay them: {{ $brand->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $brand->updated_at->format('d-m-Y') }}<br/>
<a href="{{ route('brands.edit', $brand->id) }}">Chinh sua hang xe</a>
@else
Danh sach cac hang xe hien co:
<table>
<tr>
  <td>id</td>
  <td>ten hang</td>
  <td>hanh dong</td>
</tr>
@if ($brands->count() > 0)
@foreach ($brands as $brand)
<tr>
  <td>{{ $brand->id }}</td>
  <td>{{ $brand->brand_name }}</td>
  <td>
    <a href="{{ route('brands.show', $brand->id) }}">
      Chi tiet
    </a>
    <a href="{{ route('brands.edit', $brand->id) }}">
      Chinh sua
    </a>
  </td>
</tr>
@endforeach
@else
Hien tai khong co hang xe nao!
@endif
</table>
@endif
@endsection