$(document).ready(function() {
	$('.analytics-tabs').each(function(k, v) {

		$('.analytics-nav a', v).each(function(k2, v2) {

			$(v2).click(function() {

				$('.analytics-nav a', v).removeClass('active');

				$(this).addClass('active');

				$('.analyticsTab', v).addClass('hidden');

				$($('.analyticsTab', v).get(k2)).removeClass('hidden');


				// redraw the chart
				$('body').trigger('redraw');

				// $(window).trigger('resize');

				return false;
			});
		});

		$('.analytics-nav li:first-child a', v).trigger('click');
	});

});
