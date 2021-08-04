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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
  <style type="text/css">
  html {
    height: 100%;
  }
  body {
    min-height: 100%;
    display: flex;
    flex-direction: column;
  }
  .container {
    padding-top: 75px;
  }
  footer {
    background-color: #333;
    color: #FFF;
    margin-top: auto;
    padding: 3em;
  }
  </style>
  @yield('extra-css')
</head>
<body>

  <div class="container">
    @if (Auth::check())
    @include('navbar.layouts')

    Chao <b>{{ Auth::user()->nameAndUsername() }}</b><br/>
    @endif

    @include('popup.error')

    @include('popup.notify')

    @yield('page-content')
  </div>

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
@yield('javascripts')
<footer>
  Code by Tran Hoang Quan - 19120338 <a href="https://github.com/trhgquan">(@trhgquan)</a><br/>
  <a href="https://github.com/trhgquan/bikeshop-manager">https://github.com/trhgquan/bikeshop-manager</a><br/>
</footer> 
</body>
</html>