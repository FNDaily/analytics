/**
 * Area
 */
Analytics.reports.Area = Analytics.reports.BaseChart.extend(
    {
        initChart: function() {
            this.base();

            $period = $('<div class="period" />').prependTo(this.$chart);
            $title = $('<div class="title" />').prependTo(this.$chart);
            $view = $('<div class="view" />').prependTo(this.$chart);

            $view.html(this.data.view);
            $title.html(this.data.metric);
            $period.html(this.data.periodLabel);

            this.dataTable = Analytics.Utils.responseToDataTableV4(this.data.chart, this.localeDefinition);

            this.chartOptions = Analytics.ChartOptions.area(this.data.period);

            if (typeof(this.data.chartOptions) != 'undefined') {
                $.extend(this.chartOptions, this.data.chartOptions);
            }

            if (this.data.period == 'year') {
                var dateFormatter = new google.visualization.DateFormat({
                    pattern: "MMMM yyyy"
                });

                dateFormatter.format(this.dataTable, 0);
            }

            this.chart = new google.visualization.AreaChart(this.$graph.get(0));

            this.addChartReadyListener();
        }
    });
