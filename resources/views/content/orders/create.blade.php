@extends('content.orders.layouts')

@section('page-form')
Tao mot don hang moi:<br/>
<form action="{{ route('orders.store') }}" method="POST">
@csrf
Ten nguoi dat:<br/>
<input type="text" name="customer_name" value="{{ old('customer_name') }}"/><br/>
Email nguoi dat:<br/>
<input type="email" name="customer_email" value="{{ old('customer_email') }}"/><br/>
<table name="itemsList">
  <tr>
    <td>loai xe</td>
    <td>so luong</td>
    <td></td>
  </tr>
  @if (!old('bike_id', NULL))
  <tr name="orderInfo">
    <td>
      <select name="bike_id[]">
        <option value="0">-- chon mot loai xe --</option>
        @foreach ($bikes as $bike)
          <option value="{{ $bike->id }}">
            {{ $bike->id }} - {{ $bike->bike_name }} 
            (gia ban: {{$bike->bike_sell_price }} - trong kho: {{ $bike->bike_stock }})
          </option>
        @endforeach
      </select>
    </td>
    <td>
      <input type="number" name="order_value[]" value="1"/>
    </td>
    <td><button type="button" onclick="removeItem(this);">Xoa</button></td>
  </tr>
  @else
  @foreach (old('bike_id') as $index => $bike_id)
  <tr name="orderInfo">
    <td>
      <select name="bike_id[]">
        <option value="0">-- chon mot loai xe --</option>
        @foreach ($bikes as $bike)
          <option value="{{ $bike->id }}" 
            {{ $bike_id == $bike->id ? "selected" : "" }}>
            {{ $bike->id }} - {{ $bike->bike_name }} 
            (gia ban: {{$bike->bike_sell_price }} - trong kho: {{ $bike->bike_stock }})
          </option>
        @endforeach
      </select>
      <td>
        <input type="number" name="order_value[]" 
        value="{{ old('order_value.' . $index) }}"/>
      </td>
      <td><button type="button" onclick="removeItem(this);">Xoa</button></td>
    </td>
  </tr>
  @endforeach
  @endif
</table>
Da thanh toan:
<input type="checkbox" name="order_checkout"/><br/>
<button id="addMoreItem" type="button" onclick="addItem(this);">
  Them loai xe
</button>
<button type="submit">Tao don hang</button>
</form>
@endsection

@section('javascripts')
<script type="text/javascript" src="{{ asset('js/table-action.js') }}"></script>
@endsection