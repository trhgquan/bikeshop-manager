@extends('content.bike.layouts')

@section('title', 'Thông tin loại xe ' . $bike->bike_name)

@section('page-small-title')
<small class="lead">Thông tin loại xe {{ $bike->bike_name }}</small>
@endsection

@section('page-table')
<div class="row">
  <div class="col-sm">
    <dl class="row">
      <dt class="col-sm-3">Tên loại xe</dt>
      <dd class="col-sm-9">{{ $bike->bike_name }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Hãng xe</dt>
      <dd class="col-sm-9">
        <a href="{{ route('brands.show', $bike->brand_id) }}">
          {{ $bike->brand->brand_name }}
        </a>
      </dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Giới thiệu</dt>
      <dd class="col-sm-9">{{ $bike->bike_description }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Tạo bởi</dt>
      <dd class="col-sm-9">{{ $bike->created_by->nameAndUsername() }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Ngày tạo</dt>
      <dd class="col-sm-9">{{ $bike->created_at }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Sửa lần cuối</dt>
      <dd class="col-sm-9">{{ $bike->updated_by->nameAndUsername() }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Ngày sửa</dt>
      <dd class="col-sm-9">{{ $bike->updated_at }}</dd>
    </dl>
  </div>
  <div class="col-sm">
    <dl class="row">
      <dt class="col-sm-3">Số lượng trong kho</dt>
      <dd class="col-sm-9">{{ $bike->bike_stock }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Giá nhập</dt>
      <dd class="col-sm-9">{{ $bike->bike_buy_price }}</dd>
    </dl>
    <dl class="row">
      <dt class="col-sm-3">Giá bán</dt>
      <dd class="col-sm-9">{{ $bike->bike_sell_price }}</dd>
    </dl>
  </div>
</div>
@can('update', $bike)
  <a class="btn btn-warning" href="{{ route('bikes.edit', $bike->id) }}">Chỉnh sửa loại xe {{ $bike->bike_name }}</a> 
@endcan
@endsection