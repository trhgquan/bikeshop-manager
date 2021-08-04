@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger">
      <b>Lỗi</b> {{ $error }}
    </div>
    @endforeach
@endif