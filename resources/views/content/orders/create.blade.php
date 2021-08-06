@extends('content.orders.layouts')

@section('title', 'Tạo đơn hàng mới')

@section('page-small-title')
<small class="lead">Tạo đơn hàng mới</small>
@endsection

@section('page-form')
<form action="{{ route('orders.store') }}" method="POST">
@csrf
<div class="row mb-3">
  <label for="customer_name" class="col-sm-2 col-form-control">
    Tên khách hàng
  </label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Tên khách hàng" value="{{ old('customer_name') }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="customer_email" class="col-sm-2 col-form-control">
    Email khách hàng
  </label>
  <div class="col-sm-10">
    <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="Email khách hàng" value="{{ old('customer_email') }}"/>
  </div>
</div>
<div class="row mb-3">
  <label class="col-sm-2 col-form-control">Nội dung đơn hàng</label>
  <div class="col-sm-10">
    <div name="itemsList">
      @if (!old('bike_id', NULL))
      <div name="orderInfo">
        <div class="input-group mb-3">
          <select name="bike_id[]" class="form-select">
            @foreach ($bikes as $bike)
              <option value="{{ $bike->id }}">
                {{ $bike->id }} - {{ $bike->bike_name }} 
                (giá bán: {{$bike->bike_sell_price }} - trong kho: {{ $bike->bike_stock }})
              </option>
            @endforeach
          </select>
          <input type="number" name="order_value[]" class="form-control" placeholder="Số lượng"/>
          <button class="btn btn-danger" onclick="removeItem(this);" type="button">Xóa</button>
        </div>
      </div>
      @else
        @foreach (old('bike_id') as $index => $bike_id)
        <div name="orderInfo">
          <div class="input-group mb-3">
            <select name="bike_id[]" class="form-select">
              @foreach ($bikes as $bike)
              <option value="{{ $bike->id }}" 
                {{ $bike_id == $bike->id ? "selected" : "" }}>
                {{ $bike->id }} - {{ $bike->bike_name }} 
                (gia ban: {{$bike->bike_sell_price }} - trong kho: {{ $bike->bike_stock }})
              </option>
              @endforeach
            </select>
            <input type="number" name="order_value[]" class="form-control" value={{ old('order_value.' . $index)  }} placeholder="Số lượng"/>
            <button class="btn btn-danger" onclick="removeItem(this);" type="button">Xóa</button>
          </div>
        </div>
        @endforeach
      @endif
    </div>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button type="button" class="btn btn-secondary" onclick="addItem(this);">Thêm loại xe</button>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <div class="form-check">
      <input type="checkbox" value="" class="form-check-input" id="order_checkout" name="order_checkout"/>
      <label for="order_checkout" class="form-check-label">Đã thanh toán</label>
    </div>
  </div>
</div>
<button class="btn btn-primary" type="submit">Tạo đơn hàng</button>
</form>
@endsection

@section('javascripts')
<script type="text/javascript" src="{{ asset('js/table-action.js') }}"></script>
@endsection