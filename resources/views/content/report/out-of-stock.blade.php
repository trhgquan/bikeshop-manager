@extends('content.report.layouts')

@section('title', 'San pham sap het hang - Bao cao')

@section('page-table')
@if ($items->count() > 0)
Danh sach cac item sap het hang:
@include('table.out-of-stock-list', ['items' => $items])
@else
Khong co item nao sap het hang!
@endif
@endsection