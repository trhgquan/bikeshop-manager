<table>
  <tr>
    <td>id</td>
    <td>ten hang</td>
    <td>hanh dong</td>
  </tr>
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
  </table>
  {{ $brands->links() }}