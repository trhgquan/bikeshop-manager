@extends('content.orders.layouts')

@section('page-table')
Chi tiet don hang:<br/>
<table>
  <tr>
    <td>loai xe</td>
    <td>so luong</td>
    <td>don gia</td>
    <td>thanh tien</td>
  </tr>
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
    <td>{{ $order->quantity() }}</td>
    <td></td>
    <td>{{ $order->income() }}</td>
  </tr>
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