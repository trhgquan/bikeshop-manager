<table id="brandsTable" class="table table-hover">
<thead>
  <th>ID</th>
  <th>Tên hãng</th>
  <th>Hành động</th>
</thead>
<tbody>
@foreach ($brands as $brand)
<tr>
  <td>HX-{{ $brand->id }}</td>
  <td>{{ $brand->brand_name }}</td>
  <td>
    <a class="btn btn-info" href="{{ route('brands.show', $brand->id) }}">
      Chi tiết
    </a>
    <a class="btn btn-warning" href="{{ route('brands.edit', $brand->id) }}">
      Chỉnh sửa
    </a>
  </td>
</tr>
@endforeach
</tbody>
</table>