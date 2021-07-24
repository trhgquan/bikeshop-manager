@if (@session('notify'))
<b>Thong bao</b>
<ul>
@foreach (@session('notify') as $type => $message)
<li>{{ $type }} {{ $message }}</li>
@endforeach
</ul>
@endif