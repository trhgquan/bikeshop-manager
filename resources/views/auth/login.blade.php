@include('popup.error')

@include('popup.notify')

<form action="{{ route('auth.login.handle') }}" method="POST">
Ten nguoi dung: <input type="text" name="username"/>
Mat khau: <input type="password" name="password"/>
@csrf
<button type="submit">Dang nhap</button>
</form>