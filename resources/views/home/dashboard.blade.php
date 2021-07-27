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
  <li>Quan ly don hang</li>
  <li>Bao cao</li>
  <ol>Cac loai xe sap het</ol>
  <ol>Cac loai xe ban chay</ol>
  <ol>Doanh so ban hang</ol>
</ul>
@endsection