@extends('template')

@section('page-content')
Doi mat khau cho tai khoan <b>{{ Auth::user()->nameAndUsername() }}</b>
<form action="{{ route('auth.changepassword.handle') }}" method="post">
@csrf
Mat khau hien tai:
<input type="password" name="password"/>
Mat khau moi:
<input type="password" name="new_password"/>
Nhap lai mat khau moi:
<input type="password" name="confirm_password"/>
<button type="submit">Doi mat khau</button>
</form>
@endsection