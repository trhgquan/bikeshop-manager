@extends('content.report.layouts')

@section('extra-css')
<style>
  .graph-wrap {
    width: 690px !important;
    height: 345px !important;
  }
</style>
@endsection

@section('page-table')
<div id="introduction"></div>
<table id="month-stat-detail" style="display: none;">
  <thead>
    <th>ten loai xe</th>
    <th>so luong ban ra</th>
  </thead>
  <tbody id="month-stat-detail-body">
  </tbody>
</table>

<div class="graph-wrap" id="graph-wrap" style="display: none;">
  <canvas id="myChart"></canvas>
</div>

<form id="loadMonthStat">
  @csrf
  Chon mot thang de xem:
  <input type="date" id="month"/>
  <span id="month_error"></span>
  <button type="submit">Xem</button>
</form>
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
    $('#month_error').empty();
    $('#introduction').empty();
    $('#month-stat-detail-body').empty();

    $.ajax({
      url: "{{ route('api.report.month-quantity-stat') }}",
      data: {
        month: month
      }
    }).done(function(result) {
      if (result.data.hasOwnProperty('error')) {
        let error = result.data.error.month;
        $('#month_error').html(error);
      }

      else {
        if (myChart != null) {
          myChart.destroy();
        }

        if (result.data.detail.length == 0) {
          $('#introduction').html(
            'Khong co du lieu nao trong thang ' + result.data.month
          );
        }

        else {
          $('#introduction').html(
            'Cac loai xe ban chay trong thang ' + result.data.month
          );

          // Draw the graph.
          $('#graph-wrap').show();
          myChart = new customChart(ctx, result.data.detail, 'bar');
          myChart.drawChart();

          // Fill table value.
          $('#month-stat-detail').show();
          result.data.detail.forEach(bike => {
            $('#month-stat-detail').append($('<tr>')
              .append($('<td>')
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