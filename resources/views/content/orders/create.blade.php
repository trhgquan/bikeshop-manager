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
            (gia ban: {{$bike->bike_sell_price }})
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
            (gia ban: {{$bike->bike_sell_price }})
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

<button id="addMoreItem" type="button" onclick="addItem(this);">
  Them loai xe
</button>
<button type="submit">Them</button>
</form>
@endsection

@section('javascripts')
<script type="text/javascript">
const MIN_ITEM = 1;

/**
 * Add a new Item to Order.
 */
function addItem(e) {
  // Clone item
  let item = document
      .getElementsByName('orderInfo')[0]
      .cloneNode(true);
  
  // Set value to 1
  item.children[1]
      .children[0]
      .value = 1;
  
  // Append to end of list.
  let itemList = document
      .getElementsByName('itemsList')[0]
      .appendChild(item);
}

/**
 * Remove item from order.
 */
function removeItem(e) {
  // Get total items.
  let items = document.getElementsByName('orderInfo');
  
  // If this is not the only item, then remove.
  if (items.length > MIN_ITEM) {
    let td = e.parentNode;
    let tr = td.parentNode;
    tr.parentNode.removeChild(tr);
  }
}
</script>
@endsection