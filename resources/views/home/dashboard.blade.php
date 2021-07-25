@extends('template')

@section('page-content')
<ul>
  <li>
    <a href="{{ route('brands.index') }}">
      Quan ly hang xe
    </a>
  </li>
  <li>Quan ly san pham</li>
  <li>Quan ly don hang</li>
  <li>Bao cao</li>
  <ol>Cac san pham sap het</ol>
  <ol>Cac san pham ban chay</ol>
  <ol>Doanh so ban hang</ol>
</ul>
@endsection