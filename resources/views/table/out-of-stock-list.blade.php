<table id="outOfStockTable" class="table table-hover">
<thead>
  <th>ID loại xe</th>
  <th>Tên hãng xe</th>
  <th>Tên loại xe</th>
  <th>Số lượng trong kho</th>
  <th>Hành động</th>
</thead>
<tbody>
@foreach ($items as $item)
<tr>
  <td>LX-{{ $item->id }}</td>
  <td>{{ $item->brand->brand_name }}</td>
  <td>{{ $item->bike_name }}</td>
  <td>{{ $item->bike_stock }}</td>
  <td>
    <a class="btn btn-info" href="{{ route('bikes.show', $item->id) }}">
      Chi tiết
    </a>
    @can('update', $item)
    <a class="btn btn-warning" href="{{ route('bikes.edit', $item->id) }}">
      Chỉnh sửa
    </a>
    @endcan
  </td>
</tr>
@endforeach
</tbody>
</table>