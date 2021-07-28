@extends('content.bike.layouts')
@section('page-table')
Hang xe: {{ $bike->brand->brand_name }}<br/>
Loai xe: {{ $bike->bike_name }}<br/>
Mo ta:<br/>
{{ $bike->bike_description }}<br/>
So luong trong kho: {{ $stock->stock }}<br/>
Gia nhap: {{ $stock->buy_price }}<br/>
GIa ban: {{ $stock->sell_price }}<br/>
Tao boi: {{ $bike->created_by->nameAndUsername() }}<br/>
Sua lan cuoi: {{ $bike->updated_by->nameAndUsername() }}<br/>
Ngay tao: {{ $bike->created_at->format('d-m-Y') }}<br/>
Ngay sua: {{ $bike->updated_at->format('d-m-Y') }}<br/>
<a href="{{ route('bikes.edit', $bike->id) }}">Chinh sua loai xe</a>
@endsection