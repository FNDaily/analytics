AnalyticsReportField=Garnish.Base.extend({init:function(e,t){this.$element=$("#"+e),this.$field=$(".analytics-field",this.$element),this.$metric=$(".analytics-metric select",this.$element),this.$chart=$(".chart",this.$element),this.$spinner=$(".spinner",this.$element),this.$error=$(".error",this.$element),this.elementId=$(".analytics-field",this.$element).data("element-id"),this.locale=$(".analytics-field",this.$element).data("locale"),this.metric=this.$metric.val(),this.addListener(this.$metric,"change","onMetricChange"),"undefined"!=typeof t.cachedResponse?this.parseResponse(t.cachedResponse):this.request()},onMetricChange:function(e){this.metric=$(e.currentTarget).val(),this.request()},request:function(){this.$spinner.removeClass("hidden"),this.$field.removeClass("analytics-error");var e={elementId:this.elementId,locale:this.locale,metric:this.metric};Craft.postActionRequest("analytics/reports/getElementReport",e,$.proxy(function(e){this.parseResponse(e)},this))},parseResponse:function(e){this.$spinner.addClass("hidden"),"undefined"!=typeof e.error?(this.$error.html(e.error),this.$field.addClass("analytics-error")):(e.chartOptions=Analytics.ChartOptions.field(),this.chart=new Analytics.reports.Area(this.$chart,e))}});