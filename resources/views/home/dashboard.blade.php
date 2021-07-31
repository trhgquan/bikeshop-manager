@extends('template')

@section('page-content')
<ul>
  <li>
    <a href="{{ route('brands.index') }}">
      Quan ly hang xe
    </a>
  </li>
  <li>
    <a href="{{ route('bikes.index') }}">
      Quan ly loai xe
    </a>
  </li>
  <li>
    <a href="{{ route('orders.index') }}">
      Quan ly don hang
    </a>
  </li>
  <li>Bao cao</li>
  <ol>
    <a href="{{ route('report.out_of_stock') }}">
      Cac loai xe sap het
    </a>
  </ol>
  <ol>Cac loai xe ban chay</ol>
  <ol>Doanh so ban hang</ol>
</ul>
@endsection