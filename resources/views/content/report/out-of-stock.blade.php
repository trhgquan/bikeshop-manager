@extends('content.report.layouts')

@section('page-table')
@if ($items->count() > 0)
Danh sach cac item sap het hang:
@include('table.out-of-stock-list', ['items' => $items])
@else
Khong co item nao sap het hang!
@endif
@endsection