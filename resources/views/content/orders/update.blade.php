@extends('content.orders.layouts')

@section('title', 'Chỉnh sửa đơn hàng')

@section('page-small-title')
<small class="lead">Chỉnh sửa đơn hàng</small>
@endsection

@section('page-form')

@if (! $order->getCheckedOut())
<form action="{{ route('orders.update', $order->id) }}" method="POST">
@csrf
@method('PUT')
<div class="row mb-3">
  <label for="customer_name" class="col-sm-2 col-form-label">Tên khách hàng</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $order->customer_name }}"/>
  </div>
</div>
<div class="row mb-3">
  <label for="customer_email" class="col-sm-2 col-form-label">Email khách hàng</label>
  <div class="col-sm-10">
    <input type="email" class="form-control" id="customer_email" name="customer_email" value="{{ $order->customer_email }}"/>
  </div>
</div>
<div class="row mb-3">
  <label class="col-sm-2 col-form-label">Nội dung đơn hàng</label>
  <div class="col-sm-10">
    <div name="itemsList">
      @foreach ($details as $detail)
      <div name="orderInfo">
        <div class="input-group mb-3">
          <select name="bike_id[]" class="form-select">
            @foreach ($bikes as $bike)
            <option value="{{ $bike->id }}" 
              {{ $detail->id == $bike->id ? "selected" : "" }}>
            {{ $bike->id }} - {{ $bike->bike_name }} 
            (giá bán: {{ $bike->bike_sell_price }} - trong kho: {{ $bike->bike_stock }})
            </option>
            @endforeach
          </select>
          <input type="number" name="order_value[]" class="form-control" value="{{ $detail->pivot->order_value }}"/>
          <button class="btn btn-danger" onclick="removeItem(this);" type="button">Xóa</button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button class="btn btn-secondary" type="button" onclick="addItem(this);">Thêm loại xe</button>
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
<button class="btn btn-primary" type="submit">Lưu chỉnh sửa</button>
</form>
@else
<p>Đơn hàng không thể chỉnh sửa nội dung vì khách hàng đã thanh toán! <a href="{{ route('orders.show', $order->id) }}">Chi tiết đơn hàng</a></p>
<p>Nếu là Quản lý hoặc Admin, bạn có thể xóa đơn hàng này.</p>
@endif

<hr>

<small class="lead">Xóa đơn hàng</small>
<form action="{{ route('orders.destroy', $order->id) }}" method="POST">
@csrf
@method('DELETE')
<p class="text-danger">
  Nhấn vào nút này là bạn sẽ xóa đơn hàng {{ $order->id }}. Suy nghĩ kỹ chưa?
</p>
<button type="submit" class="btn btn-danger" onclick="return confirm('Xóa đơn hàng. Đồng ý?');">sudo rm -r -f</button>
</form>
@endsection

@section('javascripts')
<script type="text/javascript" src="{{ url('js/table-action.js') }}"></script>
@endsection