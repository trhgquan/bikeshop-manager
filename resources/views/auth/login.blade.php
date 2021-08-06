@extends('template')

@section('title', 'Đăng nhập hệ thống')

@section('extra-css')
<style type="text/css">
.form-login {
  max-width: 500px;
  margin: auto;
}
input[name="username"] {
  border-bottom-left-radius: 0px;
  border-bottom-right-radius: 0px;
}
input[name="password"] {
  border-top-left-radius: 0px;
  border-top-right-radius: 0px;
}
.alert {
  max-width: 500px;
  margin: auto;
}
#separator {
  display: none;
}
.content {
  margin-left: 0px;
}
</style>
@endsection

@section('page-title')
<h1 class="page-title text-center">{{ config('app.name') }}</h1>
@endsection

@section('page-content')
<div class="text-center">
  <form class="form-login" action="{{ route('auth.login.handle') }}" method="POST">
    <div class="mb-3">
      <input class="form-control" placeholder="Tên người dùng" type="text" name="username" required/>
      <input class="form-control" placeholder="Mật khẩu" type="password" name="password" required/>
    </div>
    @csrf
    <div class="d-grid gap-2">
      <button class="btn btn-lg btn-primary btn-block" type="submit">Đăng nhập</button>
    </div>
  </form>
</div>
@endsection