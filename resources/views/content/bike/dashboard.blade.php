@extends('content.bike.layouts')

@section('title', 'Quan ly loai xe')

@section('page-table')
Danh sach cac loai xe hien co:
@if ($bikes->count() > 0)
@include('table.bike-list', ['bikes' => $bikes])
@else
Hien tai khong co loai xe nao!
@endif
@endsection