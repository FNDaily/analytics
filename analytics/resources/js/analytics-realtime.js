$(document).ready(function() {
	var refreshInterval = eval(Analytics.realtimeRefreshInterval);

	// console.log('realtime');

	analyticsRealtimeRequest();

	if(refreshInterval == null) {
		refreshInterval = 10; // Default to 10 seconds
	} else if(refreshInterval<2) { // mini 2 seconds
		refreshInterval = 100;
	}

	refreshInterval = refreshInterval * 1000; // to (ms)

	setInterval(analyticsRealtimeRequest, refreshInterval);
});

function analyticsRealtimeRequest()
{
	$('.analytics-widget-realtime').parent().parent().addClass('loading');

	var data = {

	};

	// console.log('send realtime request', data);

	Craft.postActionRequest('analytics/charts/realtime', data, function(response) {

		// realtime
		// console.log('get realtime response', response);

		$('.analytics-widget-realtime .analytics-errors-inject').html('');

		if(response.errors != false) {
			$('.analytics-widget-realtime .analytics-errors').removeClass('hidden');
			$('.analytics-widget-realtime .analytics-widget').addClass('hidden');

			$.each(response.errors, function(k, v) {
				$('<p class="error">'+v.message+'</p>').appendTo('.analytics-widget-realtime .analytics-errors-inject');
			});
		} else {

			$('.analytics-widget-realtime .analytics-errors').addClass('hidden');
			$('.analytics-widget-realtime .analytics-widget').removeClass('hidden');


			var newVisitor = response.visitorType.newVisitor;
			var returningVisitor = response.visitorType.returningVisitor;

			var calcTotal = ((returningVisitor * 1) + (newVisitor * 1));

			$('.analytics-widget-realtime .active-visitors .count').text(calcTotal);

			if (calcTotal > 0) {
				$('.analytics-widget-realtime .progress').removeClass('hidden');
				$('.analytics-widget-realtime .legend').removeClass('hidden');
			} else {
				$('.analytics-widget-realtime .progress').addClass('hidden');
				$('.analytics-widget-realtime .legend').addClass('hidden');
			}

			if(calcTotal > 0) {
				var blue = Math.round(100 * newVisitor / calcTotal);
			} else {
				var blue = 100;
			}

			var green = 100 - blue;

			$('.analytics-widget-realtime .progress-bar.blue').css('width', blue+'%');
			$('.analytics-widget-realtime .progress-bar.green').css('width', green+'%');

			$('.analytics-widget-realtime .progress-bar.blue span').text(blue+'%');
			$('.analytics-widget-realtime .progress-bar.green span').text(green+'%');

			// realtime content


			if (calcTotal > 0) {
				$('.no-active-visitors').addClass('hidden');
				$('.analytics-realtime-content table').removeClass('hidden');

				$('.analytics-realtime-content tbody').html('');

				$.each(response.content, function(k,v) {
					var row = $('<tr><td>'+k+'</td><td class="thin">'+v+'</td></td>');

					$('.analytics-realtime-content tbody').append(row);
				});
			} else {
				$('.no-active-visitors').removeClass('hidden');
				$('.analytics-realtime-content table').addClass('hidden');
			}
		}

		$('.analytics-widget-realtime').parent().parent().removeClass('loading');
	});

}
