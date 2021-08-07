<table class="table table-hover" id="bikesTable">
<thead>
  <th>ID</th>
  <th>Tên loại xe</th>
  <th>Tên hãng xe</th>
  <th>Hành động</th>
</thead>
<tbody>
@foreach ($bikes as $bike)
<tr>
  <td>LX-{{ $bike->id }}</td>
  <td>{{ $bike->bike_name }}</td>
  <td>{{ isset($brand) ? $brand->brand_name : $bike->brand->brand_name }}</td>
  <td>
    <a class="btn btn-info" href="{{ route('bikes.show', $bike->id) }}">
      Chi tiết
    </a>
    <a class="btn btn-warning" href="{{ route('bikes.edit', $bike->id) }}">
      Chỉnh sửa
    </a>
  </td>
</tr>
@endforeach
</tbody>
</table>