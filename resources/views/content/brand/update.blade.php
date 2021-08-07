@extends('content.brand.layouts')

@section('title', 'Chỉnh sửa hãng ' . $brand->brand_name)

@section('page-small-title')
<small class="lead">Chỉnh sửa hãng xe <a href="{{ route('brands.show', $brand->id) }}">{{ $brand->brand_name }}</a></small>
@endsection

@section('page-form')
<form action="{{ route('brands.update', $brand->id) }}" method="POST">
@csrf
@method('PUT')
<div class="row mb-3">
  <label for="brand_name" class="col-sm-2 col-form-label">Tên hãng xe</label>
  <div class="col-sm-10">
    <input class="form-control" type="text" id="brand_name" name="brand_name" value="{{ $brand->brand_name }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="brand_description" class="col-sm-2 col-form-label">Mô tả hãng xe</label>
  <div class="col-sm-10">
    <textarea class="form-control" id="brand_description" name="brand_description" cols="30" rows="10">{{ $brand->brand_description }}</textarea>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button class="btn btn-primary" type="submit">Lưu chỉnh sửa</button>
  </div>
</div>
</form>

<hr>

<small class="lead">Xóa hãng xe {{ $brand->brand_name }}</small>
<form action="{{ route('brands.destroy', $brand->id) }}" method="POST">
@csrf
@method('DELETE')
<p class="text-danger">Nhấn vào nút này là bạn sẽ xóa hãng xe {{ $brand->brand_name }}. Suy nghĩ kỹ chưa?</p>
<button class="btn btn-danger" type="submit" onclick="return confirm('Xóa brand và các xe của hãng. Đồng ý?');">sudo rm -r -f</button>
</form>
@endsection