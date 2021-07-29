<table>
<tr>
  <td>id</td>
  <td>ten hang</td>
  <td>ten san pham</td>
  <td>so luong hien tai</td>
  <td>hanh dong</td>
</tr>

@foreach ($items as $item)
<tr>
  <td>{{ $item->id }}</td>
  <td>{{ $item->brand->brand_name }}</td>
  <td>{{ $item->bike_name }}</td>
  <td>{{ $item->stock->stock }}</td>
  <td>
    <a href="{{ route('bikes.show', $item->id) }}">
      Chi tiet
    </a>
    <a href="{{ route('bikes.edit', $item->id) }}">
      Chinh sua
    </a>
  </td>
</tr>
@endforeach
</table>

{{ $items->links() }}