@extends('content.brand.general')

@section('page-table')
@if (isset($id))
Thong tin hang xe Lorem Ipsum:

Lorem ipsum dolor sit amet consectetur adipisicing elit. 
Nulla provident fugiat nostrum minima blanditiis. 
Expedita labore esse architecto distinctio odio?

<a href="{{ route('brand.edit.view', $id) }}">Chinh sua hang xe</a>
@else
Danh sach cac hang xe hien co:
<table>
  <tr>
    <td>id</td>
    <td>Hang xe</td>
    <td>Mieu ta</td>
    <td>Ngay them</td>
    <td>Ngay sua</td>
    <td>Thao tac</td>
  </tr>
  <tr>
    <td>1</td>
    <td>Lorem ipsum</td>
    <td>Cogito, ergo sum!</td>
    <td>22/11/2001</td>
    <td>22/11/2021</td>
    <td>
      <a href="{{ route('brand.view.id', rand(1, 100)) }}">
        xem chi tiet
      </a>
    </td>
  </tr>
</table>
@endif
@endsection