<table id="bikesTable">
<thead>
  <th>id</th>
  <th>ten xe</th>
  <th>hang xe</th>
  <th>hanh dong</th>
</thead>
<tbody>
@foreach ($bikes as $bike)
<tr>
  <td>{{ $bike->id }}</td>
  <td>{{ $bike->bike_name }}</td>
  <td>{{ isset($brand) ? $brand->brand_name : $bike->brand->brand_name }}</td>
  <td>
    <a href="{{ route('bikes.show', $bike->id) }}">
      Chi tiet
    </a>
    <a href="{{ route('bikes.edit', $bike->id) }}">
      Chinh sua
    </a>
  </td>
</tr>
@endforeach
</tbody>
</table>