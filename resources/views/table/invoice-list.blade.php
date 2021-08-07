<table class="table table-hover">
  <thead>
    <th>Loại xe</th>
    <th>Số lượng</th>
    <th>Đơn giá</th>
    <th>Thành tiền</th>
  </thead>
  <tbody>
  @foreach ($detail as $line)
  <tr>
    <td><a href="{{ route('bikes.show', $line->pivot->bike_id) }}">{{ $line->bike_name }}</a></td>
    <td>{{ $line->pivot->order_value }}</td>
    <td>{{ $line->pivot->order_sell_price }}</td>
    <td>{{ $line->pivot->order_value * $line->pivot->order_sell_price }}</td>
  </tr>
  @endforeach
  </tbody>
  <tfoot>
    <tr>
      <th>#</th>
      <th>Tổng số sản phẩm</th>
      <th>Tổng doanh thu</th>
      <th>Tổng lợi nhuận</th>
    </tr>
    <tr>
      <td>Tổng cộng</td>
      <td>{{ $order->quantity() }}</td>
      <td>{{ $order->revenue() }}</td>
      <td>{{ $order->profit() }}</td>
    </tr>
    <tr>
      <td>Trạng thái thanh toán</td>
      <td colspan="3">
        {{ $order->getCheckedOut() 
          ? 'Đã thanh toán ngày ' . $order->checkout_at->format('d-m-Y') 
          : 'Chưa thanh toán' }}
      </td>
    </tr>
  </tfoot>
</table>