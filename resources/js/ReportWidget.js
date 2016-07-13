/**
 * Report Widget
 */
Analytics.ReportWidget = Garnish.Base.extend(
{
    requestData: null,

    init: function(element, options)
    {
        this.$element = $('#'+element);
        this.$title = $('.title', this.$element);
        this.$body = $('.body', this.$element);
        this.$date = $('.date', this.$element);
        this.$spinner = $('.spinner', this.$element);
        this.$spinner.removeClass('body-loading');
        this.$error = $('.error', this.$element);


        // default/cached request

        this.chartRequest = options['request'];

        if(typeof(this.chartRequest) != 'undefined')
        {
            this.requestData = this.chartRequest;
        }


        // default/cached response

        this.chartResponse = options['cachedResponse'];

        if(typeof(this.chartResponse) != 'undefined')
        {
            this.$spinner.removeClass('hidden');

            this.parseResponse(this.chartResponse);
        }
        else if(this.requestData)
        {
            this.chartResponse = this.sendRequest(this.requestData);
        }
    },

    sendRequest: function(data)
    {
        this.$spinner.removeClass('hidden');

        $('.chart', this.$body).remove();

        this.$error.addClass('hidden');

        Craft.postActionRequest('analytics/reports/getReport', data, $.proxy(function(response, textStatus)
        {
            this.$spinner.addClass('hidden');

            if(textStatus == 'success' && typeof(response.error) == 'undefined')
            {
                this.parseResponse(response);
            }
            else
            {
                var msg = 'An unknown error occured.';

                if(typeof(response) != 'undefined' && response && typeof(response.error) != 'undefined')
                {
                    msg = response.error;
                }

                this.$error.html(msg);
                this.$error.removeClass('hidden');
            }

            window.dashboard.grid.refreshCols(true);

        }, this));
    },

    parseResponse: function(response)
    {
        $chart = $('<div class="report"></div>');
        $chart.appendTo(this.$body);

        this.$title.html(response.metric);
        this.$date.html(response.periodLabel);

        var chartType = response.type;
        chartType = chartType.charAt(0).toUpperCase() + chartType.slice(1);

        response['onAfterInit'] = $.proxy(function() {
            this.$spinner.addClass('hidden');
        }, this);

        this.chart = new Analytics.reports[chartType]($chart, response);
    }
});
