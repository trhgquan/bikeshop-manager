@extends('content.brand.layouts')

@section('page-content')
Hang xe: {{ $brand->brand_name }}<br/>
Mo ta:<br/>
{{ $brand->brand_description }}<br/>
Ngay them: {{ $brand->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $brand->updated_at->format('d-m-Y') }}<br/>
<a href="{{ route('brands.edit', $brand->id) }}">Chinh sua hang xe</a>
@endsection