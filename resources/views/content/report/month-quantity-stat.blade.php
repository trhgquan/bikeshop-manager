@extends('content.report.layouts')

@section('title', 'Sản phẩm bán chạy - Báo cáo')

@section('extra-css')
<style>
  .graph-wrap {
    width: 690px !important;
    height: 345px !important;
  }
</style>
@endsection

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
      <button class="btn btn-outline-primary" type="submit">Xem</button>
    </div>
  </div>
  <span id="month_error" class="invalid-feedback"></span>
</form>

<div id="introduction"></div>
<div class="row">
  <div class="col">
    <div class="graph-wrap" id="graph-wrap" style="display: none;">
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
  $('#loadMonthStat').on('submit', function(e) {  
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
      if (result.data.hasOwnProperty('error')) {
        let error = result.data.error.month;
        $('#month_error').html(error).show();
      }

      else {
        if (myChart != null) {
          myChart.destroy();
        }

        if (result.data.items == 0) {
          $('#introduction').html(
            'Không có dữ liệu tháng ' + result.data.month
          );
        }

        else {
          $('#introduction').html(
            'Các loại xe bán chạy trong tháng ' + result.data.month
          );

          // Draw the graph.
          $('#graph-wrap').show();
          myChart = new customChart(ctx, result.data.detail, 'bar', 
            'Số lượng bán ra trong tháng ' + result.data.month
          );
          myChart.drawChart();

          // Fill table value.
          $('#month-stat-detail').show();
          result.data.detail.forEach(bike => {
            $('#month-stat-detail').append($('<tr>')
              .append($('<td>')
                .text(bike.id)
              ).append($('<td>')
                .text(bike.bike_name)
              ).append($('<td>')
                .text(bike.bike_order_value)
              )
            )
          });
        }
      }
    });

    e.preventDefault();
  });
});
</script>
@endsection