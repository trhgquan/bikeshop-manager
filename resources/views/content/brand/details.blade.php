@extends('content.brand.layouts')

@section('page-content')
Hang xe: {{ $brand->brand_name }}<br/>
Mo ta:<br/>
{{ $brand->brand_description }}<br/>
Tao boi: {{ $brand->created_by->nameAndUsername() }}<br/>
Sua lan cuoi: {{ $brand->updated_by->nameAndUsername() }}<br/>
Ngay tao: {{ $brand->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $brand->updated_at->format('d-m-Y') }}<br/>
<a href="{{ route('brands.edit', $brand->id) }}">Chinh sua hang xe</a>
@endsection