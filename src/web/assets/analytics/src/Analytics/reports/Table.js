/**
 * Table
 */
Analytics.reports.Table = Analytics.reports.BaseChart.extend(
{
    initChart: function()
    {
        this.base();

        $period = $('<div class="period" />').prependTo(this.$chart);
        $title = $('<div class="title" />').prependTo(this.$chart);
        $view = $('<div class="view" />').prependTo(this.$chart);

        $view.html(this.data.view);
        $title.html(this.data.metric);
        $period.html(this.data.periodLabel);

        this.dataTable = Analytics.Utils.responseToDataTableV4(this.data.chart, this.localeDefinition);
        this.chartOptions = Analytics.ChartOptions.table();
        this.chart = new google.visualization.Table(this.$graph.get(0));

        this.addChartReadyListener();
    },

    resize: function()
    {
        // disable resize for the table chart
    },
});