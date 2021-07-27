@extends('template')

@section('page-content')

<b>Quan ly loai xe</b><br/>
<a href="{{ route('bikes.create') }}">Them loai xe</a><br/>
@yield('page-form')

@yield('page-table')

@endsection