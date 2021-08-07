@extends('template')

@section('title', 'Doi mat khau')

@section('page-content')

@section('page-title')
<h1 class="display-3">Đổi mật khẩu</h1>
@endsection

@section('page-small-title')
<small class="lead">Tài khoản {{ Auth::user()->nameAndUsername() }}</small>
@endsection

<form action="{{ route('auth.changepassword.handle') }}" method="post">
@csrf
<div class="row mb-3">
  <label for="password" class="col-sm-2 col-form-label">
    Mật khẩu hiện tại
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Mật khẩu hiện tại" type="password" id="password"  name="password"/>
  </div>
</div>
<div class="row mb-3">
  <label for="new_password" class="col-sm-2 col-form-label">
    Mật khẩu mới
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Mật khẩu mới" type="password" id="new_password" name="new_password"/>
  </div>
</div>
<div class="row mb-3">
  <label for="confirm_password" class="col-sm-2 col-form-label">
    Nhập lại mật khẩu mới
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Nhập lại mật khẩu mới" type="password" id="confirm_password" name="confirm_password"/>
  </div>
</div>

<button class="btn btn-lg btn-primary" type="submit">Đổi mật khẩu</button>
</form>
@endsection