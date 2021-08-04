<table id="brandsTable">
<thead>
  <th>id</th>
  <th>ten hang</th>
  <th>hanh dong</th>
</thead>
<tbody>
@foreach ($brands as $brand)
<tr>
  <td>{{ $brand->id }}</td>
  <td>{{ $brand->brand_name }}</td>
  <td>
    <a href="{{ route('brands.show', $brand->id) }}">
      Chi tiet
    </a>
    <a href="{{ route('brands.edit', $brand->id) }}">
      Chinh sua
    </a>
  </td>
</tr>
@endforeach
</tbody>
</table>