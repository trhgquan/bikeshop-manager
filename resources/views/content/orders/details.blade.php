@extends('content.orders.layouts')

@section('title', 'Chi tiet don hang')

@section('page-table')
Chi tiet don hang:<br/>
<table>
  <thead>
    <th>loai xe</th>
    <th>so luong</th>
    <th>don gia</th>
    <th>thanh tien</th>
  </thead>
  <tbody>
  @foreach ($detail as $line)
  <tr>
    <td>{{ $line->bike_name }}</td>
    <td>{{ $line->pivot->order_value }}</td>
    <td>{{ $line->pivot->order_sell_price }}</td>
    <td>{{ $line->pivot->order_value * $line->pivot->order_sell_price }}</td>
  </tr>
  @endforeach
  <tr>
    <td>Tong cong</td>
    <td colspan="2">{{ $order->quantity() }}</td>
    <td>{{ $order->income() }}</td>
  </tr>
  </tbody>
</table>

Ngay tao: {{ $order->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $order->updated_at->format('d-m-Y') }}<br/>
Nguoi tao: {{ $order->created_by->nameAndUsername() }}<br/>
Nguoi sua: {{ $order->updated_by->nameAndUsername() }}<br/>
Ngay thanh toan: 
{{ $order->getCheckedOut() ? $order->checkout_at->format('d-m-Y') : "Chua thanh toan" }}
<br/>

@if (!$order->getCheckedOut())
<a href="{{ route('orders.edit', $order->id) }}">Chinh sua don hang</a>
@endif
@endsection