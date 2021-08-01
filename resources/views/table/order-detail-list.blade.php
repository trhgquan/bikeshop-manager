<table>
  <tr>
    <td>loai xe</td>
    <td>so luong</td>
    <td>don gia</td>
    <td>thanh tien</td>
  </tr>
  @foreach ($detail as $line)
  <tr>
    <td>{{ $line->bike->bike_name }}</td>
    <td>{{ $line->order_value }}</td>
    <td>{{ $line->order_sell_price }}</td>
    <td>{{ $line->income() }}</td>
  </tr>
  @endforeach
  <tr>
    <td>Tong cong</td>
    <td>{{ $order->quantity() }}</td>
    <td></td>
    <td>{{ $order->income() }}</td>
  </tr>
</table>