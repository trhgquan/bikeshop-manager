class customChart {
  myChart = null;

  constructor(ctx, raw, type, chartLabel) {
    this.ctx = ctx;
    this.data = raw.map(a => parseInt(a.bike_order_value));
    this.labels = raw.map(a => a.bike_name);
    this.colours = Array.from(
      {length : this.data.length}, 
      () => this.getRandomColor()
    );
    this.type = type;
    this.chartLabel = chartLabel;
  }

  destroy() {
    if (this.myChart != null) {
      this.myChart.destroy();
    }
  }

  getRandomColor() {
    let letters = '0123456789ABCDEF'.split(''); // HEX color.
    let color = '#';
    for (let i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
  }

  // Load graph.
  drawChart() {
    this.myChart = new Chart(this.ctx, {
      type: this.type,
      responsive: true,
      data: {
        labels: this.labels,
        datasets: [{
          label: this.chartLabel,
          data: this.data,
          backgroundColor: this.colours,
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
  }
}