@extends('content.bike.layouts')

@section('page-table')
Danh sach cac loai xe hien co:
@if ($bikes->count() > 0)
@include('table.bike-list', ['bikes' => $bikes])
@else
Hien tai khong co loai xe nao!
@endif
@endsection