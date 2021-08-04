<table id="ordersTable">
  <thead>
    <th>ma don hang</th>
    <th>ten khach hang</th>
    <th>email khach hang</th>
    <th>ngay tao</th>
    <th>trang thai thanh toan</th>
    <th>hanh dong</th>
  </thead>
  <tbody>
  @foreach ($orders as $order)
  <tr>
    <td>{{ $order->id }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>{{ $order->customer_email }}</td>
    <td>{{ $order->created_at }}</td>
    <td>
      {{ $order->getCheckedOut() ? $order->checkout_at : "Chua thanh toan" }}
    </td>
    <td>
      <a href="{{ route('orders.show', $order->id) }}">Chi tiet</a>
      @if (!$order->getCheckedOut())
        <a href="{{ route('orders.edit', $order->id) }}">Chinh sua</a>
      @endif
    </td>
  </tr>
  @endforeach
  </tbody>
</table>