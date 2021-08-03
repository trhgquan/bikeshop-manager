@extends('content.bike.layouts')

@section('title', 'Them loai xe moi')

@section('page-form')
Them mot loai xe moi:<br/>
<form action="{{ route('bikes.store') }}" method="POST">
@csrf
Hang xe:<br/>
<select name="brand_id">
<option value="0">-- Chon hang xe --</option>
@foreach ($brands as $brand)
<option {{ old('brand_id') == $brand->id ? "selected" : ""}} 
  value="{{ $brand->id }}">
  {{ $brand->idAndName() }}
</option>
@endforeach
</select><br/>
Ten loai xe:<br/>
<input type="text" name="bike_name" value="{{ old('bike_name') }}"/><br/>
Mo ta loai xe:<br/>
<textarea name="bike_description" cols="30" rows="10">
{{ old('bike_description') }}
</textarea><br/>
So luong nhap:
<input type="number" name="bike_stock" value="{{ old('bike_stock') }}"/><br/>
Gia nhap:
<input type="number" name="bike_buy_price" value="{{ old('bike_buy_price') }}"><br/>
Gia ban:
<input type="number" name="bike_sell_price" value="{{ old('bike_sell_price') }}"><br/>
<button type="submit">Them</button>
</form>
@endsection