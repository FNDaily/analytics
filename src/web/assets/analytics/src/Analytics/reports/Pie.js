/** global: Analytics */
/** global: google */
/**
 * Pie
 */
Analytics.reports.Pie = Analytics.reports.BaseChart.extend(
    {
        initChart: function() {
            this.base();

            $period = $('<div class="period" />').prependTo(this.$chart);
            $title = $('<div class="title" />').prependTo(this.$chart);
            $view = $('<div class="view" />').prependTo(this.$chart);

            $view.html(this.data.view);
            $title.html(this.data.dimension);
            $period.html(this.data.metric + " " + this.data.periodLabel);

            this.dataTable = Analytics.Utils.responseToDataTable(this.data.chart, this.localeDefinition);
            this.chartOptions = Analytics.ChartOptions.pie();
            this.chart = new google.visualization.PieChart(this.$graph.get(0));

            this.chartOptions.height = this.$graph.height();

            this.addChartReadyListener();
        }
    });
