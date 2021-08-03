@extends('content.brand.layouts')

@section('title', 'Chinh sua hang ' . $brand->brand_name)

@section('page-form')
Chinh sua hang xe:<br/>
<form action="{{ route('brands.update', $brand->id) }}" method="POST">
@csrf
@method('PUT')
Ten hang xe:<br/>
<input type="text" name="brand_name" value="{{ $brand->brand_name }}"/><br/>
Mo ta hang xe:<br/>
<textarea name="brand_description" cols="30" rows="10">
{{ $brand->brand_description }}
</textarea><br/>
<button type="submit">Luu chinh sua</button>
</form>

Xoa hang xe:<br/>
<form action="{{ route('brands.destroy', $brand->id) }}" method="POST">
@csrf
@method('DELETE')
Nhan vao nut nay la ban se xoa hang xe {{ $brand->brand_name }}. Suy nghi ky chua?
<button type="submit" onclick="return confirm('Xoa brand va cac xe cua hang. Dong y?');">Xoa</button>
</form>
@endsection