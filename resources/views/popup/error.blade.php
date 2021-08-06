@if ($errors->any())
<div class="alert alert-danger">
  <b>Lỗi:</b>
  @if ($errors->count() > 1)
  <ul>
  @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
  @endforeach
  </ul>
  @else
  {{ $errors->first() }}
  @endif
</div>
@endif