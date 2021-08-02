<table>
  <tr>
    <td>ma don hang</td>
    <td>ten khach hang</td>
    <td>email khach hang</td>
    <td>ngay tao</td>
    <td>trang thai thanh toan</td>
    <td>hanh dong</td>
  </tr>
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
</table>

{{ $orders->links() }}