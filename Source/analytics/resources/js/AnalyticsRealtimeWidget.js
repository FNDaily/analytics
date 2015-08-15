/**
 * Realtime
 */
Analytics.Realtime = Garnish.Base.extend({

    init: function(element)
    {
        this.$element = $('#'+element);
        this.$title = $('.title', this.$element);
        this.$body = $('.body', this.$element);
        this.$spinner = $('.spinner', this.$element);

        this.$realtimeVisitors = $('.analytics-realtime-visitors', this.$element);

        this.timer = false;

        this.startRealtime();
    },

    enable: function()
    {
        this.request();
        this.startRealtime();
    },

    disable: function()
    {
        this.stopRealtime();
    },

    startRealtime: function()
    {
        if(this.timer)
        {
            this.stopRealtime();
        }

        this.timer = setInterval($.proxy(function()
        {
            this.request();

        }, this), AnalyticsRealtimeInterval * 1000);
    },

    stopRealtime: function()
    {
        clearInterval(this.timer);
    },

    request: function()
    {
        this.$spinner.removeClass('body-loading');
        this.$spinner.removeClass('hidden');

        Craft.queueActionRequest('analytics/reports/getRealtimeReport', {}, $.proxy(function(response, textStatus)
        {
            if(textStatus == 'success' && typeof(response.error) == 'undefined')
            {
                this.$realtimeVisitors.removeClass('hidden');
                // this.explorer.$error.addClass('hidden');
                this.handleResponse(response);
            }
            else
            {
                msg = 'An unknown error occured.';

                if(typeof(response) != 'undefined' && response && typeof(response.error) != 'undefined')
                {
                    msg = response.error;
                }

                this.$realtimeVisitors.addClass('hidden');
                // this.explorer.$error.html(msg);
                // this.explorer.$error.removeClass('hidden');
            }

            this.$spinner.addClass('hidden');

        }, this));
    },

    handleResponse: function(response)
    {
        var newVisitor = response.newVisitor;
        var returningVisitor = response.returningVisitor;

        var calcTotal = ((returningVisitor * 1) + (newVisitor * 1));

        $('.active-visitors .count', this.$realtimeVisitors).text(calcTotal);

        if (calcTotal > 0) {
            $('.progress', this.$realtimeVisitors).removeClass('hidden');
            $('.legend', this.$realtimeVisitors).removeClass('hidden');
        }
        else
        {
            $('.progress', this.$realtimeVisitors).addClass('hidden');
            $('.legend', this.$realtimeVisitors).addClass('hidden');
        }

        if(calcTotal > 0)
        {
            var blue = Math.round(100 * newVisitor / calcTotal);
        }
        else
        {
            var blue = 100;
        }

        var green = 100 - blue;

        // blue

        $('.progress-bar.blue', this.$realtimeVisitors).css('width', blue+'%');
        $('.progress-bar.blue span', this.$realtimeVisitors).text(blue+'%');

        if(blue > 0)
        {
            $('.progress-bar.blue', this.$realtimeVisitors).removeClass('hidden');
        }
        else
        {
            $('.progress-bar.blue', this.$realtimeVisitors).addClass('hidden');
        }

        // green

        $('.progress-bar.green', this.$realtimeVisitors).css('width', green+'%');
        $('.progress-bar.green span', this.$realtimeVisitors).text(green+'%');

        if(green > 0)
        {
            $('.progress-bar.green', this.$realtimeVisitors).removeClass('hidden');
        }
        else
        {
            $('.progress-bar.green', this.$realtimeVisitors).addClass('hidden');
        }
    },
});
