@extends('content.report.layouts')

@section('page-table')
Danh sach cac item sap het hang:
@include('table.out-of-stock-list', ['items' => $items])
@endsection