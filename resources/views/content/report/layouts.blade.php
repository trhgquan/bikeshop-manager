@extends('template')

@section('page-content')
<h1 class="page-title">
  Báo cáo
</h1>
<small class="text-muted">@yield('small-title')</small>
<hr>
@yield('page-table')

@yield('page-stats')

@endsection