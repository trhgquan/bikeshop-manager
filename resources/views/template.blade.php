<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  @if (Auth::check())
  <meta name="api_token" content="{{ Auth::user()->api_token }}">
  @endif
  <title>@yield('title')</title>
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
  @yield('extra-css')
</head>
<body>

@if (Auth::check())
@include('navbar.layouts')

Chao <b>{{ Auth::user()->nameAndUsername() }}</b><br/>

@include('popup.error')

@include('popup.notify')
@endif

@yield('page-content')

@if (Auth::check())
<script type="text/javascript">
// Script for logout.
let logoutBtn = document.getElementById('logoutBtn');
let logoutForm = document.getElementById('logoutForm');
logoutBtn.addEventListener('click', function() {
  logoutForm.submit();
});
</script>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
@yield('javascripts')      
</body>
<footer>
  Code by Tran Hoang Quan - 19120338 <a href="https://github.com/trhgquan">(@trhgquan)</a><br/>
  <a href="https://github.com/trhgquan/bikeshop-manager">https://github.com/trhgquan/bikeshop-manager</a><br/>
</footer>
</html>