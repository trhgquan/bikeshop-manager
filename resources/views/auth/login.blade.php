@if ($errors->any())
<b>Loi:</b>
<ul>
  @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
  @endforeach
</ul>
@endif
<form action="{{ route('auth.login.authenticate') }}" method="POST">
Ten nguoi dung: <input type="text" name="username"/>
Mat khau: <input type="password" name="password"/>
<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
<button type="submit">Dang nhap</button>
</form>