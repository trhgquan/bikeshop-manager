@extends('content.users.layouts')

@section('title', 'Quản lý người dùng')

@section('page-small-title')
<small class="lead">Danh sách người dùng trên hệ thống</small>
@endsection

@section('page-table')
<table class="table table-hover" id="usersList">
  <thead>
    <th>ID người dùng</th>
    <th>Tên tài khoản</th>
    <th>Quyền</th>
    <th>Hành động</th>
  </thead>
  <tbody>
    @foreach ($users as $user)
    <tr>
      <td>NV-{{ $user->id }}</td>
      <td>{{ $user->username }}</td>
      <td>{{ $user->roles->role_name }}</td>
      <td>
        @can('update', $user)
          <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Chỉnh sửa</a>
        @endcan
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="{{ asset('js/vn-datatable.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#usersList').DataTable(settings);
});
</script>
@endsection