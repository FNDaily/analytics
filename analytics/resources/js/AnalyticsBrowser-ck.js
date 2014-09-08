google.load("visualization","1",{packages:["corechart","table","geochart"]});AnalyticsBrowser=Garnish.Base.extend({init:function(e){this.currentTableType="table";this.currentPeriod="month";this.chart=!1;this.chartData=!1;this.table=!1;this.tableData=!1;this.pie=!1;this.table=!1;this.$element=$("#"+e);this.$widget=$(".analytics-widget:first",this.$element);this.$browser=$(".analytics-browser:first",this.$widget);this.$chart=$(".analytics-chart",this.$browser).get(0);this.$pie=$(".analytics-piechart",this.$browser).get(0);this.$table=$(".analytics-table",this.$browser).get(0);this.$dimension=$(".analytics-dimension select",this.$browser);this.$metric=$(".analytics-metric select",this.$browser);this.$total=$(".analytics-total",this.$browser);this.$tableType=$(".analytics-tabletype:first",this.$browser);this.$tableTypeBtns=$(".analytics-tabletype .btn",this.$browser);this.$period=$(".analytics-period:first",this.$browser);this.$periodBtns=$(".analytics-period .btn",this.$browser);this.$spinner=$(".spinner",this.$browser);this.$spinner.removeClass("body-loading");this.addListener(this.$tableTypeBtns,"click",{},function(e){this.onTableTypeChange($(e.currentTarget).data("tabletype"))});this.addListener(this.$periodBtns,"click",{},function(e){this.onPeriodChange($(e.currentTarget).data("period"));this.browse()});this.changeCurrentNav("audience");this.$dimension.change($.proxy(function(){this.browse()},this));this.$metric.change($.proxy(function(){this.browse()},this));$(window).resize($.proxy(function(){this.resize()},this))},resize:function(){this.chart&&this.chart.draw(this.chartData,this.chartOptions);this.table&&this.table.draw(this.tableData,this.tableOptions);this.pie&&this.pie.draw(this.tableData,this.pieOptions)},changeCurrentNav:function(e){this.$currentNav=e;this.$dimension.html("");$.each(AnalyticsBrowserSections,$.proxy(function(e,t){e==this.$currentNav&&$.each(t.dimensions,$.proxy(function(e,t){$('<option value="'+e+'">'+t+"</option>").appendTo(this.$dimension)},this))},this));this.$metric.html("");$.each(AnalyticsBrowserSections,$.proxy(function(e,t){e==this.$currentNav&&$.each(t.metrics,$.proxy(function(e,t){$('<option value="'+e+'">'+t+"</option>").appendTo(this.$metric)},this))},this));this.browse()},onTableTypeChange:function(e){this.currentTableType=e;this.$tableTypeBtns.removeClass("active");$('[data-tabletype="'+e+'"]',this.$tableType).addClass("active");if(e=="table"){$(".analytics-table",this.$browser).removeClass("hidden");$(".analytics-piechart",this.$browser).addClass("hidden")}else{$(".analytics-table",this.$browser).addClass("hidden");$(".analytics-piechart",this.$browser).removeClass("hidden")}},onPeriodChange:function(e){this.currentPeriod=e;this.$periodBtns.removeClass("active");$('[data-period="'+e+'"]',this.$period).addClass("active")},browse:function(){this.$spinner.removeClass("hidden");var e={id:this.$widget.data("widget-id"),dimension:this.$dimension.val(),metric:this.$metric.val(),period:this.currentPeriod};Craft.postActionRequest("analytics/browse/combined",e,$.proxy(function(e){if(typeof e.error=="undefined"){this.updateChart(e.chart);this.updateTable(e.table);this.updateTotal(e.total)}else console.log("error");this.$spinner.addClass("hidden")},this))},updateChart:function(e){this.chartData=new google.visualization.DataTable;$.each(e.columns,$.proxy(function(e,t){var n=AnalyticsUtils.parseColumn(t);this.chartData.addColumn(n.type,n.label)},this));this.chartData.addRows(e.rows);this.chart||(this.chart=new google.visualization.AreaChart(this.$chart));this.chart.draw(this.chartData,this.chartOptions)},updateTotal:function(e){$(".analytics-count",this.$total).html(e.count);$(".analytics-label",this.$total).html(e.label)},updateTable:function(e){this.tableData=new google.visualization.DataTable;$.each(e.columns,$.proxy(function(e,t){var n=AnalyticsUtils.parseColumn(t);this.tableData.addColumn(n.type,n.label)},this));this.tableData.addRows(e.rows);this.table||(this.table=new google.visualization.Table(this.$table));this.table.draw(this.tableData,this.tableOptions);this.pie||(this.pie=new google.visualization.PieChart(this.$pie));this.pie.draw(this.tableData,this.pieOptions)},chartOptions:{theme:"maximized",legend:"none",backgroundColor:"#FFF",colors:["#058DC7"],areaOpacity:.1,pointSize:8,lineWidth:4,chartArea:{},hAxis:{textPosition:"in",textStyle:{color:"#058DC7"},showTextEvery:5,baselineColor:"#fff",gridlines:{color:"none"}},vAxis:{textPosition:"in",textStyle:{color:"#058DC7"},baselineColor:"#ccc",gridlines:{color:"#fafafa"},maxValue:0}},pieOptions:{theme:"maximized",pieHole:.5,legend:{alignment:"center",position:"top"},chartArea:{top:40,height:"82%"},sliceVisibilityThreshold:0},tableOptions:{page:"enable"}});