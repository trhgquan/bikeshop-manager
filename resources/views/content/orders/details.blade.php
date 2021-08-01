@extends('content.orders.layouts')

@section('page-table')
Chi tiet don hang:<br/>
@include('table.order-detail-list', compact('detail'))

Ngay tao: {{ $order->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $order->updated_at->format('d-m-Y') }}<br/>
Nguoi tao: {{ $order->created_by->nameAndUsername() }}<br/>
Nguoi sua: {{ $order->updated_by->nameAndUsername() }}<br/>
Ngay thanh toan: 
{{ $order->getCheckedOut() ? $order->checkout_at->format('d-m-Y') : "Chua thanh toan" }}

<a href="{{ route('orders.edit', $order->id) }}">Chinh sua don hang</a>
@endsection