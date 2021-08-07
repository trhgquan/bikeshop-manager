@if (@session('notify'))
  @foreach (@session('notify') as $type => $message)
  <div class="alert alert-{{ $type }}">
    <b>Thông báo</b> {{ $message }}
  </div>
  @endforeach
@endif