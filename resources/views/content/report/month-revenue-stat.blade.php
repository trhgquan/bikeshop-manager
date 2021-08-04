@extends('content.report.layouts')

@section('title', 'Doanh so ban hang - Bao cao')

@section('page-table')
<div id="introduction"></div>
<table id="revenueTable" style="display: none;">
  <thead>
    <th>id don hang</th>
    <th>so luong san pham</th>
    <th>doanh thu</th>
    <th>loi nhuan</th>
  </thead>
  <tbody id="revenueTableContent"></tbody>
  <tfoot>
    <tr>
      <td>Tong so don hang</td>
      <td>Tong so san phan</td>
      <td>Tong doanh thu</td>
      <td>Tong loi nhuan</td>
    </tr>
    <tr id="revenueTableConclusion"></tr>
  </tfoot>
</table>

<form id="loadMonthStat">
  Chon thang:
  <input type="date" id="month"/>
  <span id="month_error"></span>
  <button type="submit">Xem</button>
</form>
@endsection

@section('javascripts')
<script type="text/javascript">
$(document).ready(function() {
  $('#loadMonthStat').on('submit', function(e) {
    let month = $('#month').val();
    $('#revenueTable').hide();
    $('#introduction').empty();
    $('#month_error').empty();
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
        $('#month_error').html(result.data.errors.month);
      }

      else {
        if (result.data.items == 0) {
          $('#introduction').html(
            'Khong co bao cao trong thang ' + result.data.month
          );
        }

        else {
          $('#introduction').html(
            'Bao cao doanh thu thang ' + result.data.month
          );

          // Load table
          result.data.detail.forEach(order => {
            $('#revenueTableContent').append($('<tr>')
              .append($('<td>')
                .text(order.id)
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