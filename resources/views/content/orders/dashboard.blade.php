@extends('content.orders.layouts')

@section('page-table')
Danh sach cac don hang tren he thong:
@if ($orders->count() > 0)
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
    <td>{{ $order->created_at->format('d-m-Y h:m:s') }}</td>
    <td>
      {{ $order->getCheckedOut() ? $order->checkout_at->format('d-m-Y') : "Chua thanh toan" }}
    </td>
    <td>
      <a href="{{ route('orders.show', $order->id) }}">Chi tiet</a>
      <a href="{{ route('orders.edit', $order->id) }}">Chinh sua</a>
    </td>
  </tr>
  @endforeach
</table>

{{ $orders->links() }}
@else
Hien tai khong co don hang nao!
@endif
@endsection