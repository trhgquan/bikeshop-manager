@extends('content.users.layouts')

@section('title', 'Chỉnh sửa người dùng ' . $user->name)

@section('page-small-title')
<small class="lead">Chỉnh sửa người dùng {{ $user->nameAndUsername() }}</small>
@endsection

@section('page-form')
<form action="{{ route('users.update', $user) }}" method="POST">
@csrf
@method('PUT')
<small class="lead">Chỉnh sửa thông tin</small>
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

<small class="lead">Xóa tài khoản</small>

<hr>

<form action="{{ route('users.destroy', $user) }}" method="POST">
@csrf
@method('DELETE')
<p class="text-danger">Nhấn nút này sẽ xóa tài khoản {{ $user->nameAndUsername() }}. Đồng ý?</p>
<button type="submit" class="btn btn-danger">sudo rm -r -f</button>
</form>
@endsection