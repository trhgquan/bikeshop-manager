@extends('content.orders.layouts')

@section('title', 'Chinh sua don hang')

@section('page-form')
Chinh sua don hang:<br/>
<form action="{{ route('orders.update', $order->id) }}" method="POST">
@csrf
@method('PUT')
Ten khach hang:<br/>
<input type="text" name="customer_name" value="{{ $order->customer_name }}"/><br/>
Email khach hang:<br/>
<input type="email" name="customer_email" value="{{ $order->customer_email }}"/><br/>
<table name="itemsList">
  <tr>
    <td>loai xe</td>
    <td>so luong</td>
    <td></td>
  </tr>
  @foreach ($details as $detail)
  <tr name="orderInfo">
    <td>
      <select name="bike_id[]">
        <option value="0">-- chon mot loai xe --</option>
        @foreach ($bikes as $bike)
          <option value="{{ $bike->id }}" 
            {{ $detail->id == $bike->id ? "selected" : "" }}>
            {{ $bike->id }} - {{ $bike->bike_name }} 
            (gia ban: {{ $bike->bike_sell_price }} - trong kho: {{ $bike->bike_stock }})
          </option>
        @endforeach
      </select>
      <td>
        <input type="number" name="order_value[]" 
        value="{{ $detail->pivot->order_value }}"/>
      </td>
      <td><button type="button" onclick="removeItem(this);">Xoa</button></td>
    </td>
  </tr>
  @endforeach
</table>
<button id="addMoreItem" type="button" onclick="addItem(this);">
  Them loai xe
</button><br/>
Da thanh toan: 
<input type="checkbox" name="order_checkout"/><br/>
<button type="submit">Luu chinh sua</button>
</form>

Xoa don hang:<br/>
<form action="{{ route('orders.destroy', $order->id) }}" method="POST">
@csrf
@method('DELETE')
Nhan vao nut nay la ban se xoa don hang {{ $order->id }}. Suy nghi ky chua?
<button type="submit" onclick="return confirm('Xoa don hang. Dong y?');">Xoa</button>
</form>
@endsection

@section('javascripts')
<script type="text/javascript" src="{{ url('js/table-action.js') }}"></script>
@endsection