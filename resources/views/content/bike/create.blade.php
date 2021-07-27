@extends('content.bike.layouts')

@section('page-form')
Them mot hang xe moi:<br/>
<form action="{{ route('bikes.store') }}" method="POST">
@csrf
Hang xe:<br/>
<select name="brand_id">
<option value="0">-- Chon hang xe --</option>
@foreach ($brands as $brand)
<option {{ old('brand_id') == $brand->id ? "selected" : ""}} 
  value="{{ $brand->id }}">
  {{ $brand->brand_name }}
</option>
@endforeach
</select><br/>
Ten loai xe:<br/>
<input type="text" name="bike_name" value="{{ old('bike_name') }}"/><br/>
Mo ta loai xe:<br/>
<textarea name="bike_description" cols="30" rows="10">
{{ old('bike_description') }}
</textarea>
<button type="submit">Them</button>
</form>
@endsection