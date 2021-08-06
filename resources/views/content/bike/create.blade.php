@extends('content.bike.layouts')

@section('title', 'Thêm loại xe mới')

@section('page-small-title')
<small class="lead">Thêm một loại xe mới</small>
@endsection

@section('page-form')
<form action="{{ route('bikes.store') }}" method="POST">
@csrf
<div class="row mb-3">
  <label for="brand_id" class="col-sm-2 col-form-label">Hãng xe</label>
  <div class="col-sm-10">
    <select id="brand_id" class="form-select" name="brand_id">
      <option value="0">-- Chọn một hãng xe --</option>
      @foreach ($brands as $brand)
      <option {{ old('brand_id') == $brand->id ? "selected" : ""}} 
        value="{{ $brand->id }}">
        {{ $brand->idAndName() }}
      </option>
      @endforeach
    </select>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    Không có hãng? <a class="btn btn-success" href="{{ route('brands.create') }}">Tạo mới!</a>
  </div>
</div>
<div class="row mb-3">
  <label for="bike_name" class="col-sm-2 col-form-label">
    Tên loại xe
  </label>
  <div class="col-sm-10">
    <input type="text" placeholder="Tên loại xe" class="form-control" id="bike_name" name="bike_name" value="{{ old('bike_name') }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="bike_description" class="col-sm-2 col-form-label">
    Mô tả loại xe
  </label>
  <div class="col-sm-10">
    <textarea placeholder="Mô tả loại xe" class="form-control" id="bike_description" name="bike_description" cols="5" rows="5">{{ old('bike_description') }}</textarea>
  </div>
</div>
<div class="row mb-3">
  <label for="bike_stock" class="col-sm-2 col-form-label">
    Số lượng
  </label>
  <div class="col-sm-10">
    <input placeholder="Số lượng" class="form-control" type="number" id="bike_stock" name="bike_stock" value="{{ old('bike_stock') }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="bike_buy_price" class="col-sm-2 col-form-label">
    Giá nhập
  </label>
  <div class="col-sm-10">
    <input placeholder="Giá nhập" class="form-control" type="number" id="bike_buy_price" name="bike_buy_price" value="{{ old('bike_buy_price') }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="bike_sell_price" class="col-sm-2 col-form-label">
    Giá bán
  </label>
  <div class="col-sm-10">
    <input placeholder="Giá bán" class="form-control" type="number" id="bike_sell_price" name="bike_sell_price" value="{{ old('bike_sell_price') }}"/>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button class="btn btn-primary" type="submit">Thêm loại xe</button>
  </div>
</div>

</form>
@endsection