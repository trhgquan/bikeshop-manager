@extends('template')

@section('page-content')

<b>Quan ly don hang</b><br/>
<a href="{{ route('orders.create') }}">Tao mot don hang moi</a><br/>
@yield('page-form')

@yield('page-table')

@endsection