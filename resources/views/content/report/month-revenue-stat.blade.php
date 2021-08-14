@extends('content.report.layouts')

@section('title', 'Doanh số bán hàng - Báo cáo')

@section('page-small-title')
<small class="lead">Doanh số bán hàng theo tháng</small>
@endsection

@section('page-table')
<form id="loadMonthStat">
  <div class="row g-3">
    <div class="col">
      <label for="month">Chọn tháng:</label>
    </div>
    <div class="col">
      <input class="form-control" type="date" id="month"/>
    </div>
    <div class="col">
      <button class="btn btn-outline-primary" type="submit">Xem</button>
    </div>
  </div>
  <span id="month_error" class="invalid-feedback"></span>
</form>

<div id="introduction"></div>
<table class="table table-hover" id="revenueTable" style="display: none;">
  <thead>
    <th>Mã đơn hàng</th>
    <th>Số lượng sản phẩm bán ra</th>
    <th>Doanh thu</th>
    <th>Lợi nhuận</th>
  </thead>
  <tbody id="revenueTableContent"></tbody>
  <tfoot>
    <tr>
      <th>Tổng số đơn hàng</th>
      <th>Tổng số sản phẩm bán ra</th>
      <th>Tổng doanh thu</th>
      <th>Tổng lợi nhuận</th>
    </tr>
    <tr id="revenueTableConclusion"></tr>
  </tfoot>
</table>
@endsection

@section('javascripts')
<script type="text/javascript">
$(document).ready(function() {
  $('#loadMonthStat').on('submit', function(e) {
    let month = $('#month').val();
    $('#revenueTable').hide();
    $('#introduction').empty();
    $('#month_error').empty().hide();
    $('#revenueTableContent').empty();
    $('#revenueTableConclusion').empty();

    $.ajax({
      url: "{{ route('api.report.month-revenue-stat') }}",
      data: {
        api_token: $('[name="api_token"]').attr('content'),
        month: month
      }
    }).done(function(result) {
      if (result.data.hasOwnProperty('errors')) {
        $('#month_error').html(result.data.errors.month).show();
      }

      else {
        if (result.data.items == 0) {
          $('#introduction').html(
            'Không có số liệu trong tháng ' + result.data.month
          );
        }

        else {
          $('#introduction').html(
            'Báo cáo doanh số tháng ' + result.data.month
          );

          // Load table
          let url = "{{ route('orders.show', ':id') }}";

          result.data.detail.forEach(order => {
            url = url.replace(':id', order.id);
            $('#revenueTableContent').append($('<tr>')
              .append($('<td>')
                .append($('<a>')
                  .text('DH-' + order.id)
                  .attr('href', url)
                )
              ).append($('<td>')
                .text(order.quantity)
              ).append($('<td>')
                .text(order.revenue)
              ).append($('<td>')
                .text(order.profit)
              ));
          });

          // Load conclusion
          $('#revenueTableConclusion').append($('<td>')
              .text(result.data.items)
            ).append($('<td>')
              .text(result.data.total.quantity)
            ).append($('<td>')
              .text(result.data.total.revenue)
            ).append($('<td>')
              .text(result.data.total.profit)
            );

          $('#revenueTable').show();
        }
      }
    });

    e.preventDefault();
  });
});
</script>
@endsection