@extends('content.brand.layouts')

@section('title', 'Thêm hãng xe mới')

@section('page-small-title')
<small class="lead">Thêm hãng xe mới</small>
@endsection

@section('page-form')
<form action="{{ route('brands.store') }}" method="POST">
@csrf
<div class="row mb-3">
  <label for="brand_name" class="col-form-label col-sm-2">Tên hãng xe</label>
  <div class="col-sm-10">
    <input type="text" placeholder="Tên hãng xe" name="brand_name" id="brand_name" class="form-control" value="{{ old('brand_name') }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="brand_description" class="col-form-label col-sm-2">Mô tả hãng xe</label>
  <div class="col-sm-10">
    <textarea class="form-control" placeholder="Mô tả hãng xe" id="brand_description" name="brand_description" cols="30" rows="10">{{ old('brand_description') }}</textarea>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button class="btn btn-primary" type="submit">Thêm hãng xe</button>
  </div>
</div>
</form>
@endsection