@extends('content.report.layouts')

@section('extra-css')
<style>
  .graph-wrap {
    width: 400px !important;
    height: 400px !important;
  }
</style>
@endsection

@section('page-table')
Danh sach cac item ban chay trong thang {{ \Carbon\Carbon::now()->month }}:
{{-- <table>
  <tr>
    <td>ten hang xe</td>
    <td>so luong</td>
  </tr>
</table> --}}

<div class="graph-wrap">
  <canvas id="myChart"></canvas>
</div>
@endsection

@section('javascripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js"></script>
<script type="text/javascript">
/**
 * Get a random color.
 *
 * @return string
 */
function getRandomColor() {
  let letters = '0123456789ABCDEF'.split(''); // HEX color.
  let color = '#';
  for (let i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

// Load graph.
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Antonov', 'Sukhoi', 'Mikoyan'],
    datasets: [{
      label: 'Value saled',
      data: [12, 19, 3],
      backgroundColor: [
        getRandomColor(),
        getRandomColor(),
        getRandomColor()
      ],
      hoverOffset: 4
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
</script>
@endsection