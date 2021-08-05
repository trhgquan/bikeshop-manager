@extends('template')

@section('title', 'Dashboard')

@section('page-content')
Ban muon lam gi hom nay?
<a href="{{ route('brands.index') }}">Tao hang xe moi</a>
<a href="{{ route('bikes.index') }}">Them loai xe moi</a>
<a href="{{ route('orders.index') }}">Tao don hang moi</a>
<a href="{{ route('report.month_revenue_stat.index') }}">Xem doanh so</a>
@endsection