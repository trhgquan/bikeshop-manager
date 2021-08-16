@extends('content.users.layouts')

@section('title', 'Chỉnh sửa người dùng ' . $user->name)

@section('page-small-title')
<small class="lead">Chỉnh sửa người dùng {{ $user->nameAndUsername() }}</small>
@endsection

@section('page-form')
<form action="{{ route('users.update', $user) }}" method="POST">
@csrf
@method('PUT')
<small class="lead">Chỉnh sửa quyền</small>
<hr>
<div class="row mb-3">
  <label for="role" class="col-sm-2 col-form-label">Quyền</label>
  <div class="col-sm-10">
    <select name="role" id="role" class="form-select">
      @foreach ($roles as $role)
        <option value="{{ $role->id }}" {{ $role->id == $user->role ? 'selected' : '' }}>{{ $role->role_name }}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button type="submit" class="btn btn-primary">Lưu sửa đổi</button>
  </div>
</div>
</form>

<small class="lead">Đặt mật khẩu mới</small>
<hr>
<form action="{{ route('users.update', $user) }}" method="POST">
@csrf
@method('PUT')
<div class="row mb-3">
  <label for="new_password" class="col-sm-2 col-form-label">Mật khẩu mới</label>
  <div class="col-sm-10">
    <input type="password" class="form-control" placeholder="Nhập mật khẩu mới" name="new_password" id="new_password">
  </div>
</div>
<div class="row mb-3">
  <label for="re_password" class="col-sm-2 col-form-label">Nhập lại mật khẩu mới</label>
  <div class="col-sm-10">
    <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới" name="re_password" id="re_password">
  </div>
</div>
<div class="row mb-3">
  <div class="col-sm-2"></div>
  <div class="col-sm-10">
    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
  </div>
</div>
</form>

<small class="lead">Xóa tài khoản</small>

<hr>

<form action="{{ route('users.destroy', $user) }}" method="POST">
@csrf
@method('DELETE')
<p class="text-danger">Nhấn nút này sẽ xóa tài khoản {{ $user->nameAndUsername() }}. Đồng ý?</p>
<button type="submit" class="btn btn-danger" onclick="return confirm('Xóa tài khoản khỏi hệ thống?');">sudo rm -r -f</button>
</form>
@endsection