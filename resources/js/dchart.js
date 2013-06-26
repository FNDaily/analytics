var charts = {};

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(function() {
      // initialize all dcharts once ready
});

(function($){
   var Dchart = function(element, dataArray, jsonOptions)
   {
       var elem = $(element);
       var obj = this;

       // Merge options with defaults
       var settings = $.extend(true, {
            options: {

            },
            chartQuery: {

            },
            chartOptions: {
              chartType: 'line',
              title:'Default Title'
            }

       }, jsonOptions);

       console.log('### JSONOPTIONS', jsonOptions);
       console.log('### SETTINGS', settings);
       // Public method

       this.drawChart = function()
       {
            var data = google.visualization.arrayToDataTable(dataArray);



            if(settings.chartOptions.chartType == 'donut') {
                console.log('### DONUT');

                var chartOptions = $.extend({
                      title: settings.chartOptions.title,
                      legend:'none',
                      sliceVisibilityThreshold:1/50,
                      pieHole:0.5,
                      chartArea : {width:'90%'},
               }, settings.chartOptions || {});

                var chart = new google.visualization.PieChart(element);

            } else {
                console.log('### PIE');

                var chartOptions = $.extend({
                    title: settings.chartOptions.title,
                    legend:{position:'bottom'},
                    chartArea : {width:'90%'},
                    vAxis: {minValue: 4, format: '#'},
                    hAxis: {minValue: 4, format: '#', showTextEvery:5}
                 }, settings.chartOptions || {});

                var chart = new google.visualization.LineChart(element);
            }

            chart.draw(data, chartOptions);

            console.log('publicMethod() called!');
       };

      $(window).resize(function() {
            obj.drawChart();
      });
   };

   $.fn.dchart = function(dataArray, jsonOptions)
   {
       return this.each(function()
       {
           var dchart = new Dchart(this, dataArray, jsonOptions);
           dchart.drawChart();
       });
   };
})(jQuery);