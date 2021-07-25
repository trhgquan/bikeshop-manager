@extends('template')

@section('page-content')

<b>Quan ly hang xe</b><br/>
<a href="{{ route('brands.create') }}">Them hang xe</a><br/>
@yield('page-form')

@yield('page-table')

@endsection