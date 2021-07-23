Chao mung {{ Auth::user()->name }}
<ul>
  <li><a href="#">Chinh sua thong tin tai khoan</a></li>
  <li>
    <form id="logoutForm" action="{{ route('auth.logout') }}" method="POST">
      <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
      <a type="submit" href="#" id="logoutBtn">Dang xuat</a>
    </form>
  </li>
</ul>

<script type="text/javascript">
let logoutBtn = document.getElementById('logoutBtn');
let logoutForm = document.getElementById('logoutForm');
logoutBtn.addEventListener('click', function() {
  logoutForm.submit();
});
</script>