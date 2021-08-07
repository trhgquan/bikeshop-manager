@extends('template')

@section('page-title')
<h1 class="display-3">Quản lý người dùng</h1>
@endsection

@section('page-content')

@if (! Request::routeIs('users.create'))
<a class="btn btn-success" href="{{ route('users.create') }}">Thêm người dùng mới</a>
@endif

@yield('page-form')

@yield('page-table')

@endsection