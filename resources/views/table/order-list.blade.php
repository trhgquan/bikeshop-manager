<table class="table table-hover" id="ordersTable">
  <thead>
    <th>Mã đơn hàng</th>
    <th>Tên khách hàng</th>
    <th>Email khách hàng</th>
    <th>Ngày tạo</th>
    <th>Trạng thái thanh toán</th>
    <th>Hành động</th>
  </thead>
  <tbody>
  @foreach ($orders as $order)
  <tr>
    <td>{{ $order->id }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>{{ $order->customer_email }}</td>
    <td>{{ $order->created_at }}</td>
    <td>
      {{ $order->getCheckedOut() ? $order->checkout_at : "Chưa thanh toán" }}
    </td>
    <td>
      <a class="btn btn-info" href="{{ route('orders.show', $order->id) }}">
        Chi tiết
      </a>
      @if (!$order->getCheckedOut())
        <a class="btn btn-warning" href="{{ route('orders.edit', $order->id) }}">
          Chỉnh sửa
        </a>
      @endif
    </td>
  </tr>
  @endforeach
  </tbody>
</table>