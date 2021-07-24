Chao <b>{{ Auth::user()->name }}</b>

@include('navbar.general')

@include('popup.error')

@include('popup.notify')

@yield('page-content')

<script type="text/javascript">
let logoutBtn = document.getElementById('logoutBtn');
let logoutForm = document.getElementById('logoutForm');
logoutBtn.addEventListener('click', function() {
  logoutForm.submit();
});
</script>