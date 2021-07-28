@extends('content.bike.layouts')

@section('page-form')
Chinh sua loai xe:<br/>
<form action="{{ route('bikes.update', $bike->id) }}" method="POST">
@csrf
@method('PUT')
Hang xe:<br/>
<select name="brand_id">
<option value="0">-- Chon hang xe --</option>
@foreach ($brands as $brand)
<option value="{{ $brand->id }}" {{ $brand->id == $bike->brand->id ? "selected" : "" }}>
  {{ $brand->brand_name }}
</option>
@endforeach
</select><br/>
Ten loai xe:<br/>
<input type="text" name="bike_name" value="{{ $bike->bike_name }}"/><br/>
Mo ta loai xe:<br/>
<textarea name="bike_description" cols="30" rows="10">
{{ $bike->bike_description }}
</textarea>
<button type="submit">Luu chinh sua</button>
</form>

Xoa loai xe:<br/>
<form action="{{ route('bikes.destroy', $bike->id) }}" method="POST">
@csrf
@method('DELETE')
Nhan vao nut nay la ban se xoa loai xe {{ $bike->bike_name }}. Suy nghi ky chua?
<button type="submit" onclick="return confirm('Xoa loai xe. Dong y?');">Xoa</button>
</form>
@endsection