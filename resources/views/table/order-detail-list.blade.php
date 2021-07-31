<table>
  <tr>
    <td>loai xe</td>
    <td>so luong</td>
    <td>don gia</td>
    <td>thanh tien</td>
  </tr>
  @foreach ($detail as $line)
  <tr>
    <td>{{ $line->bike_id }}</td>
    <td>{{ $line->order_value }}</td>
    <td>{{ $line->order_sell_price }}</td>
    <td>{{ $line->order_value * $line->order_sell_price }}</td>
  </tr>
  @endforeach
</table>