@extends('content.orders.layouts')

@section('page-table')
Ngay tao: {{ $order->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $order->updated_at->format('d-m-Y') }}<br/>
Nguoi tao: {{ $order->created_by->nameAndUsername() }}<br/>
Nguoi sua: {{ $order->updated_by->nameAndUsername() }}<br/>
Trang thai thanh toan: 
{{ $order->getCheckedOut() ? $order->checkout_at->format('d-m-Y') : "Chua thanh toan" }}

@include('table.order-detail-list', compact('detail'))

<a href="{{ route('orders.edit', $order->id) }}">Chinh sua don hang</a>
@endsection