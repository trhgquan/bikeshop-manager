@extends('template')

@section('page-title')
<h1 class="display-3">
  Báo cáo
</h1>
@endsection

@section('page-content')

@yield('page-table')

@yield('page-stats')

@endsection