@extends('content.orders.layouts')

@section('title', 'Quản lý đơn hàng')

@section('page-small-title')
<small class="lead">Danh sách các đơn hàng trên hệ thống</small>
@endsection

@section('page-table')
@if ($orders->count() > 0)
<form id="loadMonthOrder">
  <div class="row g-3">
    <div class="col">
      <label for="month">Chọn tháng:</label>
    </div>
    <div class="col">
      <input class="form-control" type="date" id="month"/>
    </div>
    <div class="col">
      <button id="submitBtn" class="btn btn-outline-primary" type="submit">Xem</button>
    </div>
  </div>
  <span id="month_error" class="invalid-feedback"></span>
</form>
<hr>
<small class="lead" id="introduction">Tất cả đơn hàng trên hệ thống</small>
@include('table.order-list', compact('orders'))
@else
Hiện tại không có đơn hàng nào!
@endif
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="{{ asset('js/vn-datatable.js') }}"></script>
<script type="text/javascript">
var table;
$(document).ready(function() {
  // No order since results were ordered by created_at at query.
  settings.order = [];
  table = $('#ordersTable').DataTable(settings);
});

$('#loadMonthOrder').on('submit', function(e) {
    $('#submitBtn').attr('disabled', 'disabled');
    table.destroy();
    $('#ordersTableBody').empty();
    $('#introduction').empty();
    $('#month_error').empty();

    $.ajax({
      url: "{{ route('api.orders.month') }}",
      data: {
        month: $('#month').val(),
        api_token: $('[name="api_token"]').attr('content'),
      }
      
    }).done(function(result) {
      if (result.data.hasOwnProperty('errors')) {
        $('#month_error').html(result.data.errors.month).show();
      }

      else {
        if (result.data.items == 0) {
          $('#month_error').html(
            'Không có số liệu trong tháng ' + result.data.month
          ).show();
        }

        else {
          $('#introduction').html(
            'Các đơn hàng trong tháng ' + result.data.month
          );

          result.data.detail.forEach(order => {
            $('#ordersTableBody').append($('<tr>')
              .append(
                $('<td>').text('DH-' + order.id)
              ).append(
                $('<td>').text(order.customer_name)
              ).append(
                $('<td>').text(order.customer_email)
              ).append(
                $('<td>').text(order.created)
              ).append(
                $('<td>').text(order.checkout)
              ).append(
                $('<td>').append(
                  $('<a>').text('Chi tiết')
                        .attr('href', order.detail_url)
                        .attr('class', 'btn btn-info')
                )
              ));
          });
        }
      }

      table = $('#ordersTable').DataTable(settings);
    });
    e.preventDefault();

    setTimeout(function() {
      $('#submitBtn').removeAttr('disabled');
    }, 3000);
  });
</script>
@endsection