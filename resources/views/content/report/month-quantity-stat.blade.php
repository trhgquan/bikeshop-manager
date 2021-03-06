@extends('content.report.layouts')

@section('title', 'Loại xe bán chạy - Báo cáo')

@section('page-small-title')
<small class="lead">Loại xe bán chạy trong tháng</small>
@endsection

@section('page-table')
<form id="loadMonthStat">
  @csrf
  <div class="row">
    <div class="col">
      <label for="month">Chọn tháng để xem:</label>
    </div>
    <div class="col">
      <input type="date" class="form-control" id="month"/>
    </div>
    <div class="col">
      <button id="submitBtn" class="btn btn-outline-primary" type="submit">Xem</button>
    </div>
  </div>
  <span id="month_error" class="invalid-feedback"></span>
</form>

<div id="introduction"></div>
<div class="row">
  <div class="col">
    <div id="graph-wrap" style="display: none;">
      <canvas id="myChart"></canvas>
    </div>
  </div>
  <div class="col">
    <table class="table table-hover" id="month-stat-detail" style="display: none;">
      <thead>
        <th>ID</th>
        <th>Tên loại xe</th>
        <th>Số lượng bán ra</th>
      </thead>
      <tbody id="month-stat-detail-body">
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js"></script>
<script type="text/javascript" src="{{ asset('js/chart-action.js') }}"></script>
<script type="text/javascript">
let ctx = document.getElementById('myChart').getContext('2d');
let myChart = null;

$(document).ready(function() {
  $('#loadMonthStat').submit(function(e) {
    $('#submitBtn').attr('disabled', 'disabled');

    let month = $('#month').val();
    $('#graph-wrap').hide();
    $('#month-stat-detail').hide();
    $('#month_error').empty().hide();
    $('#introduction').empty();
    $('#month-stat-detail-body').empty();

    $.ajax({
      url: "{{ route('api.report.month-quantity-stat') }}",
      data: {
        api_token: $('[name="api_token"]').attr('content'),
        month: month
      }
    }).done(function(result) {
      if (result.data.hasOwnProperty('errors')) {
        let error = result.data.errors.month;
        $('#month_error').html(error).show();
      }

      else {
        if (myChart != null) {
          myChart.destroy();
        }

        if (result.data.items == 0) {
          $('#month_error').html(
            'Không có dữ liệu tháng ' + result.data.month
          ).show();
        }

        else {
          $('#introduction').html(
            'Các loại xe bán chạy trong tháng ' + result.data.month
          );

          // Draw the graph.
          $('#graph-wrap').show();
          myChart = new customChart(ctx, result.data.detail, 'pie', 
            'Số lượng bán ra trong tháng ' + result.data.month
          );
          myChart.drawChart();

          // Fill table value.
          $('#month-stat-detail').show();
          result.data.detail.forEach(bike => {
            $('#month-stat-detail').append($('<tr>')
              .append($('<td>')
                .text('LX-' + bike.id)
              ).append($('<td>')
                .append($('<a>')
                  .attr('href', bike.url)
                  .text(bike.bike_name)
                )
              ).append($('<td>')
                .text(bike.bike_order_value)
              )
            )
          });
        }
      }
    });

    e.preventDefault();

    // Prevent user spamming the button.
    setTimeout(function() {
      $('#submitBtn').removeAttr('disabled');
    }, 3000);
  });
});
</script>
@endsection