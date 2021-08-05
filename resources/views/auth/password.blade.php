@extends('template')

@section('title', 'Doi mat khau')

@section('page-content')
<h1 class="page-title">Đổi mật khẩu</h1>

<form class="form-inline" action="{{ route('auth.changepassword.handle') }}" method="post">
@csrf
<label for="password" class="control-label">
  Mật khẩu hiện tại
</label>
<input class="form-control" type="password" id="password"  name="password"/>
<label for="new_password">
  Mật khẩu mới
</label>
<input class="form-control" type="password" id="new_password" name="new_password"/>
<label for="confirm_password">
  Nhập lại mật khẩu mới
</label>
<input class="form-control" type="password" id="confirm_password" name="confirm_password"/>
<button class="btn btn-lg btn-primary" type="submit">Đổi mật khẩu</button>
</form>
@endsection