@extends('content.users.layouts')

@section('title', 'Thêm người dùng mới')

@section('page-small-title')
<small class="lead">Thêm người dùng mới</small>
@endsection

@section('page-form')
<form action="{{ route('users.store') }}" method="POST">
@csrf
<div class="row mb-3">
  <label for="name" class="col-sm-2 col-form-label">
    Họ và tên
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Họ và tên" value="{{ old('name') }}" type="text" id="name" name="name"/>
  </div>
</div>
<div class="row mb-3">
  <label for="email" class="col-sm-2 col-form-label">
    Email
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Email" value="{{ old('email') }}" type="email" id="email" name="email"/>
  </div>
</div>
<div class="row mb-3">
  <label for="username" class="col-sm-2 col-form-label">
    Tên người dùng
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Tên người dùng" value="{{ old('username') }}" type="text" id="username" name="username"/>
  </div>
</div>
<div class="row mb-3">
  <label for="password" class="col-sm-2 col-form-label">
    Mật khẩu
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Mật khẩu" type="password" id="password" name="password"/>
  </div>
</div>
<div class="row mb-3">
  <label for="re_password" class="col-sm-2 col-form-label">
    Nhập lại Mật khẩu
  </label>
  <div class="col-sm-10">
    <input class="form-control" placeholder="Nhập lại Mật khẩu" type="password" id="re_password" name="re_password"/>
  </div>
</div>
<div class="row mb-3">
  <label for="role" class="col-sm-2 col-form-label">
    Quyền
  </label>
  <div class="col-sm-10">
    <select name="role" id="role" class="form-select">
      @foreach ($roles as $role)
        <option value="{{ $role->id }}"
          @if(old('role', NULL) != NULL)
            {{ $role->id == old('role') ? 'selected' : '' }}
          @endif
          >{{ $role->role_name }}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button class="btn btn-primary" type="submit">Tạo tài khoản mới</button>
  </div>
</div>
</form>
@endsection